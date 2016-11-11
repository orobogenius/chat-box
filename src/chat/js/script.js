/*
	#####################################################


	A simple Chat-Box app that emulates real-time chat

	@author Orobogenius

	#####################################################
*/
//Register this application's namespace
if (typeof ChatBox == "undefined") {
    var ChatBox = {};
};

//Create a Chat prototype under the application namespace
ChatBox.Chat = function(user) {
    //The current authenticated user
    this.user = user;
    //The state of the application
    //This @var will take one of the following values at any instance of the app
    // ** Idle - When the application is doing nothing
    // ** Loading - When the application is loading a content
    // ** Updating - When the application is updating chats
    // ** Updated - When the application has updated any given chat
    this.state = 'idle';
    //The current number of messages in a single chat
    this.numMsg = 0;
};

//Updates the a single chat view
//This function can be called Periodically to update chats from other users
ChatBox.Chat.prototype.updateChat = function() {
     //Check the state of the chatbox to see if chat can be updated
     if (ChatBox.state == "no-chat") {
	return;
     }
    //Change the application's state to Updating
    this.state = "updating";
    //Get updates
    $.ajax({
        data: {
            userID: $('#current-chat').val(),
            action: 'updateChat',
            numMsg: ChatBox.Chat.numMsg
        },
        dataType: 'json',
        success: (function(response) {
            ChatBox.Chat.numMsg = response.numMsg; //upate the number of messages in chat
            profile_pic = ChatBox.validateImage(response.contact_data.profile_pic);
            $('#current-chat-contact').html(
                '<input type="hidden" value="' + response.contact_data.userID + '" id="current-chat" />' +
                '<img class="contact-image" src="profile_pics/' + profile_pic + '" />' +
                '<div class="status" id="status"> &nbsp;' +
                response.contact_data.name +
                '</div>' +
                '<hr style="border-color: #222D32;" />'
            );
            ChatBox.chatComponents.chat.find($('div.loader')).remove();
            ChatBox.User.userdata.profile_pic = ChatBox.validateImage(ChatBox.User.userdata.profile_pic);
            $.each(response.chats, function(index, chat) {
                ChatBox.chatComponents.chat.append(
                    function() {
                        return (chat.chatBy == response.contact_data.userID)
                            //Other party's message
                            ?
                            '<div class="message-body">' +
                            '<img src="profile_pics/' + profile_pic + '" class="contact-image contact-image-right" />' +
                            '<div class="message them" style="display: inline-block">' +
                            '<p class="name" style="color: #394AA6; font-weight: bold;">' +
                            response.contact_data.name + '<span class="pull-right admin-time"><small><i class="fa fa-clock-o"></i> ' + chat.created_at + '</small></span>' +
                            '</p>' +
                            '<p>' +
                            chat.message +
                            '</p>' +
                            '</div>' +
                            '</div>'
                            //User message
                            :
                            '<div class="message-body">' +
                            '<div class="message you" style="display: inline-block">' +
                            '<p class="name" style="color: #394AA6; font-weight: bold;">' +
                            ChatBox.User.userdata.name + '<span class="pull-left admin-time"><small><i class="fa fa-clock-o"></i> ' + chat.created_at + '</small></span>' +
                            '</p>' +
                            '<p>' +
                            chat.message +
                            '</p>' +
                            '</div>' +
                            '<img src="profile_pics/' + ChatBox.User.userdata.profile_pic + '" class="contact-image contact-image-left" />' +
                            '</div>'
                    }
                );
            });
            //Adjust the scroll bar to show new chats
            document.getElementById('chat').scrollTop = document.getElementById('chat').scrollHeight;
        }).bind(this),
        error: (function(response) {})
    });
}

//Sends a chat
ChatBox.Chat.prototype.send = function(message) {
    //Update chat before sending a new chat
    //this.updateChat();
    changeBtnState($('#send'), 'loading'); //Change the Send's button state
    $.ajax({
        data: {
            userID: $('#current-chat').val(),
            message: message,
            action: 'saveChat'
        },
        //dataType: 'json',
        success: (function(response) {
            changeBtnState($('#send'), 'hide');
            if (response == 'success') {
                $('#message-box').val('');
                this.updateChat();
            } else {
                alert("Unable to send chat at this moment, please check your internet connection to make sure it's working. If this error persists, kindly contact support.");
            }
        }).bind(this),
        error: (function(response) {})
    });
}

//Initializes the chat box with user's chat data
ChatBox.Chat.prototype.init = function() {
    //Setup Ajax
    $.ajaxSetup({
        method: 'POST',
        url: 'controllers/chatboxController.php',
    });
    this.changeState('loading');
    //Get Chat Data
    $.ajax({
        data: {
            userID: this.user.userID,
            action: 'getChatData'
        },
        dataType: 'json',
        success: (function(response) {
            this.user.contacts = response.contacts;
            this.user.requests = response.requests;
            this.user.userdata = response.userdata;
            ChatBox.User.userdata = response.userdata;
            ChatBox.showProfile(this.user.userdata);
            $.each(this.user.contacts, function(index, contact) {
                ChatBox.addToContactList(contact);
            });
            ChatBox.chatComponents.requestList.append('<li class="dropdown-header"><h4 style="text-align: justify; font-weight: bold; color: #000000;">Requests</h4></li>');
            if (this.user.requests.length == 0) {
                ChatBox.chatComponents.requestList.append('<h3 style="color: #000000; text-align: center;">No Requests Available</h3>');
            }
            $.each(this.user.requests, function(index, request) {
                ChatBox.addToRequestList(request);
            });
            $('#admin-notif-num').html(this.user.requests.length);
            ChatBox.chatComponents.chat.html('<div class="nochats">Please select a chat to start messaging</div>');
            //We're done here. Change app's state to idle
            this.changeState('idle');
        }).bind(this),
        error: (function(response) {}).bind(this)
    });
}

//Change the state of the app
ChatBox.Chat.prototype.changeState = function(value) {
    this.state = value;
    switch (this.state) {
        case 'loading':
            $.each(ChatBox.chatComponents, function(index, value) {
                value.html(ChatBox.loader);
            });
            break;
        case 'idle':
            $.each(ChatBox.chatComponents, function(index, value) {
                value.find($('div.loader')).remove();
            });
            break;
    }
}

//A user object that holds data for the current user
ChatBox.User = function(userID) {
    this.userID = userID;
    this.contacts = {};
    this.requests = {};
    this.userdata = {};
}

ChatBox.loader = '<div class="loader"></div>';

//The state of the ChatBox
ChatBox.state = "no-chat";

//Validates an image resource
ChatBox.validateImage = function(value) {
    return (value == null) ? "profile.jpg" : value;
};

//Events for the ChatBox
ChatBox.events = {
    search: function(query) {
        ChatBox.Chat.state = "loading";
        ChatBox.chatComponents.chat.html(ChatBox.loader);
        //Get Search Data
        $.ajax({
            data: {
                userID: query,
                action: 'getSearchData'
            },
            dataType: 'json',
            success: (function(response) {
                if (response.userdata !== null) {
                    $.each(response.userdata, function(index, data) {
                        profile_pic = ChatBox.validateImage(data.profile_pic);
                        ChatBox.chatComponents.chat.html(
                            '<div class="search-result">' +
                            '<div class="panel">' +
                            '<div class="panel-heading">' +
                            '<h1 class="panel-title"><i class="fa fa-group"></i> People - Search Result</h1>' +
                            '</div>' +
                            '<div class="panel-body">' +
                            '<div class="contact" id="' + data.userID + '">' +
                            '<img class="contact-image-search" src="profile_pics/' + profile_pic + ' "/>' +
                            '<div class="search-data">' +
                            '<p style="font-weight: bolder;">' +
                            data.name +
                            '</p>' +
                            '<div class="actions">' +
                            '<button class="btn btn-primary" onclick="sendRequest()" action="send-request"><i class="fa fa-plus-circle"></i> Send Request</button>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>' +
                            '</div>'
                        );
                    });
                } else {
                    ChatBox.chatComponents.chat.html('<div class="nochats">No result found, please try again!</div>');
                }
            }).bind(this),
            error: (function(response) {}).bind(this)
        });
    },
    showChat: function(id) {
        ChatBox.Chat.state = "loading";
        ChatBox.chatComponents.chat.html(ChatBox.loader);
        $.ajax({
            data: {
                userID: id,
                action: 'getChat'
            },
            dataType: 'json',
            success: (function(response) {
                ChatBox.state = "chat";
                ChatBox.Chat.state = "idle";
                ChatBox.Chat.numMsg = response.numMsg;
                //console.log(ChatBox.Chat.numMsg);
                profile_pic = ChatBox.validateImage(response.contact_data.profile_pic);
                $('#current-chat-contact').html(
                    '<input type="hidden" value="' + response.contact_data.userID + '" id="current-chat" />' +
                    '<img class="contact-image" src="profile_pics/' + profile_pic + '" />' +
                    '<div class="status" id="status">&nbsp;``' +
                    response.contact_data.name +
                    '</div>' +
                    '<hr style="border-color: #222D32;" />'
                );
                ChatBox.chatComponents.chat.find($('div.loader')).remove();
                if (ChatBox.User.userdata.profile_pic === null) {
                    ChatBox.User.userdata.profile_pic = "profile.jpg";
                }
                $.each(response.chats, function(index, chat) {
                    ChatBox.chatComponents.chat.append(
                        function() {
                            return (chat.chatBy == response.contact_data.userID)
                                //Other party's message
                                ?
                                '<div class="message-body">' +
                                '<img src="profile_pics/' + profile_pic + '" class="contact-image contact-image-right" />' +
                                '<div class="message them" style="display: inline-block">' +
                                '<p class="name" style="color: #394AA6; font-weight: bold;">' +
                                response.contact_data.name + '<span class="pull-right admin-time"><small><i class="fa fa-clock-o"></i> ' + chat.created_at + '</small></span>' +
                                '</p>' +
                                '<p>' +
                                chat.message +
                                '</p>' +
                                '</div>' +
                                '</div>'
                                //User message
                                :
                                '<div class="message-body">' +
                                '<div class="message you" style="display: inline-block">' +
                                '<p class="name" style="color: #394AA6; font-weight: bold;">' +
                                ChatBox.User.userdata.name + '<span class="pull-left admin-time"><small><i class="fa fa-clock-o"></i> ' + chat.created_at + '</small></span>' +
                                '</p>' +
                                '<p>' +
                                chat.message +
                                '</p>' +
                                '</div>' +
                                '<img src="profile_pics/' + ChatBox.User.userdata.profile_pic + '" class="contact-image contact-image-left" />' +
                                '</div>'
                        }
                    );
                });
                document.getElementById('chat').scrollTop = document.getElementById('chat').scrollHeight;
                //alert(response);
            }),
            error: (function(response) {})
        });
    },
    sendRequest: function(id) {
        $.ajax({
            data: {
                userID: id,
                action: 'sendRequest'
            },
            //dataType: 'json',
            success: (function(response) {
                alert(response);
            }),
            error: (function(response) {})
        });
    },
    acceptRequest: function(el, id) {
        $.ajax({
            data: {
                userID: id,
                action: 'acceptRequest'
            },
            //dataType: 'json',
            success: (function(response) {
                if (response == "success") {
                    alert("This contact has been added to your contacts");
                    $(el).closest('li#' + id).hide('slow', function() {
                        $(this).remove();
                    });
                } else {
                    alert("Unable to add this contact to your contacts, please try again.");
                }
            }),
            error: (function(response) {})
        });
    },
    declineRequest: function(el, id) {
        if (confirm("Are you sure you want to decline this request?")) {
            $.ajax({
                data: {
                    userID: id,
                    action: 'declineRequest'
                },
                //dataType: 'json',
                success: (function(response) {
                    if (response == "success") {
                        alert("You have decline this request successfully");
                        $(el).closest('li#' + id).hide('slow', function() {
                            $(this).remove();
                        });
                    } else {
                        alert("Unable to decline this request, please try again.");
                    }
                }).bind(this),
            }).bind(this)
            error: (function(response) {});
        }
    },
    uploadImage: function() {
        $.ajax({
            contentType: false,
            processData: false,
            data: new FormData(document.getElementById('picture-form')),
            success: function(response) {
                if (response == "Too large") {
                    alert("The size of the image is too large, please ensure that the image is less than 700kB!");
                } else if (response == "Invalid Fmt") {
                    alert("The image format is not acceptable. Please convert you image into a jpg format before uploaded!");
                } else if (response == "Success") {
                    alert("The image has been uploaded successfully!");
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                }
            }
        });
    },
    removeImage: function() {
        if (confirm("Are you sure you want to remove this image?")) {
            var data = {
                action: "removeImage"
            };
            $.ajax({
                data: data,
                success: function(response) {
                    if (response == "failed") {
                        alert("There was an error removing the picture, please try again. If this error persists please contact support.");
                    } else if (response == "success") {
                        alert("The image has been removed from your display picture!");
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    }
                }
            });
        }
    }
}

//The ChatBox's chat components.
//Essentially, these are the various html entities that makes up the view
ChatBox.chatComponents = {
    profile: $('.profile'),
    contactList: $("#contact-list"),
    requestList: $("#request-list"),
    chat: $('#chat')
}

ChatBox.getUserID = function() {
    //Get userID from DOM
    return $('#userID').val();
}

//Shows the user mini-profile
ChatBox.showProfile = function(userdata) {
    profile_pic = ChatBox.validateImage(userdata.profile_pic);
    this.chatComponents.profile.prepend(
        '<div class="profile-pic">' +
        '<img class="circle-image" src="profile_pics/' + profile_pic + ' "/>' +
        '</div>' +
        '<div class="moniker">' +
        userdata.name +
        '</div>' +
        '<a href="#" id="cam-icon"><span class="glyphicon glyphicon-camera"></span></a>' +
        '<form action="" method="post" id="picture-form" enctype="multipart/form-data">' +
        '<input type="file" id="pic" name="profilePic" style="display: none;" accept="image/*" />' +
        '</form>' +
        '<ul id="upload-menu">' +
        '<li>' +
        '<a href="#" id="upload-pic" >Upload Image</a>' +
        '</li>' +
        '<li>' +
        '<a href="#" id="remove-pic">Remove Image</a>' +
        '</li>' +
        '</ul>'
    );
}

ChatBox.addToContactList = function(contact) {
    profile_pic = ChatBox.validateImage(contact.c_userdata.profile_pic);
    this.chatComponents.contactList.append(
        '<li id="' + contact.contactID + '">' +
        '<div class="contact">' +
        '<a href="#" action="show-chat" onclick="showChat(this)">' +
        '<img class="contact-image" src="profile_pics/' + profile_pic + ' "/>' +
        '<span>  ' + contact.c_userdata.name + '</span>' +
        '</a>' +
        '</div>' +
        '</li>'
    );
}

ChatBox.addToRequestList = function(request) {
    profile_pic = ChatBox.validateImage(request.c_userdata.profile_pic);
    this.chatComponents.requestList.append(
        '<li id="' + request.contactID + '">' +
        '<div class="contact">' +
        '<img class="contact-image" src="profile_pics/' + profile_pic + ' "/>' +
        '<span style="color: #000000; margin-left: 10px; font-weight: bold">' + request.c_userdata.name + '</span>' +
        '<span class="actions pull-right">' +
        '<button class="btn btn-primary btn-md" style="background: #00A65A;" onclick="acceptRequest(this)" action="accept-request"><i class="fa fa-check"></i> Accept</button>' +
        '<button class="btn btn-primary btn-md" style="background: #DD4B39;" onclick="declineRequest(this)" action="decline-request"><i class="fa fa-remove"></i> Decline</button>' +
        '</span>' +
        '</div>' +
        '</li>'
    );
}

$(document).ready(function() {

    //Init User
    var user = new ChatBox.User(ChatBox.getUserID());

    //Create a new Chat
    var chat = new ChatBox.Chat(user);

    //Prepare Chat
    chat.init();

    //Periodically upate chat when there's a current chat
    setInterval(chat.updateChat, 1000);

    $("#search").on('keypress', function(e) {
        if (e.which == 13) {
            ChatBox.events.search($(this).val());
        }
    });

    $('.navbar-toggle').on('click', function() {
        $('#admin-sidebar-nav').css('visibility', 'visible');
    });

    $('li.dropdown a').on('click', function() {
        $(this).parent().toggleClass('open');
    });

    $('#send').on('click', function() {
        if (ChatBox.state == "no-chat") {
            alert("Please select a contact before sening message");
        } else {
            if ($('#message-box').val() == "") {
                return;
            }
            chat.send($('#message-box').val());
        }
    });

    $('body').on('click', '#cam-icon', function() {
        $('#upload-menu').toggle('display');
    });

    $('body').on('click', '#upload-pic', function() {
        $('#pic').trigger('click');
    });

    $('body').on('click', '#remove-pic', function() {
        ChatBox.events.removeImage();
    });

    $('body').on('change', '#pic', function() {
        ChatBox.events.uploadImage();
    });

});

function sendRequest() {
    ChatBox.events.sendRequest($('button[action="send-request"]').closest($('div.contact')).attr('id'));
}

function acceptRequest(e) {
    ChatBox.events.acceptRequest(e, $('button[action="accept-request"]').closest('li').attr('id'));
}

function declineRequest(e) {
    ChatBox.events.declineRequest(e, $('button[action="decline-request"]').closest('li').attr('id'));
}

function showChat(e) {
    id = $(e).closest('li').attr('id');
    ChatBox.events.showChat(id);
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    return !(charCode > 31 && (charCode < 48 || charCode > 57));
}

function changeBtnState($el, status) {
    switch (status) {
        case 'loading':
            $el.find('img')
                .addClass('icon-spin')
                .css('display', 'inline');
            $el.prop('disabled', true);
            break;
        case 'hide':
            $el.find('img')
                .removeClass('icon-spin')
                .css('display', 'none');
            $el.prop('disabled', false);
            break;
    }
}
