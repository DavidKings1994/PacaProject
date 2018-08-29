<template id="">
    <div class="well">
        <div class="panel panel-default">
            <div class="panel-heading"><h3>Change profile picture</h3></div>
            <div class="panel-body">
                <form enctype="multipart/form-data" class="form-inline" id="profilePicForm">
                    <div class="col-md-2">
                        <img id="img-upload" class="img-thumbnail" :src="profilePic"
                        data-toggle="tooltip" data-placement="bottom" title="Profile pictures are cropped and centered to fit 150x150 pixels"/>
                    </div>
                    <div class="col-md-10" style="text-align: left;">
                        <div class="input-group">
                            <span class="input-group-btn">
                                <span class="btn btn-primary btn-file" style="border-radius: 4px;">
                                    <i class="glyphicon glyphicon-camera"></i> Upload image
                                    <input type="file" id="imgInp" name="file">
                                </span>
                            </span>
                        </div>
                        <div class="input-group" v-if="newImage">
                            <button type="button" name="button" class="btn btn-success" v-on:click="uploadImage">
                                <i class="glyphicon glyphicon-floppy-disk"></i>
                                Save profile picture
                            </button>
                        </div>
                        <div class="input-group" v-if="hasImage">
                            <button type="button" name="button" class="btn btn-danger" v-on:click="deleteImage">
                                <i class="glyphicon glyphicon-trash"></i>
                                Delete profile picture
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading"><h3>Change password</h3></div>
            <div class="panel-body">
                <form>
                    <div class="form-group">
                        <input type="password" class="form-control" id="pass" placeholder="New password" required="true">
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control" id="newpass" placeholder="Confirm new password" required="true">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-danger" v-on:click="changePassword">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script type="text/javascript">
var navigation = require('./../../navigation.js');
var messageStore = require('./../../messages.js');
export default {
    data: function() {
        return {
            profile: {},
            newImage: false
        }
    },
    props: ['id'],
    computed: {
        hasImage: function() {
            return this.profile != null ? (this.profile.image != null ? true: false) : false;
        },
        profilePic: function() {
            let placeholder = '/assets/profile_pics/avatar_placeholder.png';
            return this.profile != null ? (this.profile.image != null ? this.profile.image : placeholder) : placeholder;
        }
    },
    created: function() {
        this.loadUserInfo().then(() => {

        });
    },
    mounted: function() {
        $(document).ready(() => {
            let self = this;
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#img-upload').attr('src', e.target.result);
                        self.newImage = true;
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }

            $("#imgInp").change(function(){
                readURL(this);
            });
        });
    },
    methods: {
        loadUserInfo: function() {
            return new Promise(resolve => {
                $.post('./php/controllers/userController.php', {
                    action: 'getProfile',
                    name: this.id
                }, (msg) => {
                    let json = JSON.parse(msg);
                    if (json.status == 'OK') {
                        this.profile = json.profile;
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Ups! User does\'t exist',
                            type: 'error'
                        });
                    }
                    resolve();
                });
            });
        },
        uploadImage: function() {
            let formData = new FormData;
            formData.append("file", $("#imgInp")[0].files[0]);
            formData.append("action", "uploadProfilePic");
            formData.append("id", this.profile.idUser);
            $.ajax({
                url: './php/controllers/userController.php',
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                cache : false,
                success: (msg) => {
                    let json = JSON.parse(msg);
                    if (json.status != 'ERROR') {
                        this.newImage = false;
                        this.loadUserInfo();
                        messageStore.commit('addMessage', {
                            text: 'Profile picture uploaded!',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Ups! there was an error somewhere',
                            type: 'error'
                        });
                    }
                }
            });
        },
        deleteImage: function() {
            $.ajax({
                url: './php/controllers/userController.php',
                type: "POST",
                data: {
                    action: 'deleteProfilePic',
                    user: this.profile.idUser,
                    url: this.profile.image
                },
                success: (msg) => {
                    let json = JSON.parse(msg);
                    if (json.status != 'ERROR') {
                        this.loadUserInfo();
                        messageStore.commit('addMessage', {
                            text: 'Profile picture deleted!',
                            type: 'success'
                        });
                    } else {
                        messageStore.commit('addMessage', {
                            text: 'Ups! there was an error somewhere',
                            type: 'error'
                        });
                    }
                }
            });
        },
        changePassword: function() {
            if (/([^\s])/.test($('input#pass').val().trim()) && /([^\s])/.test($('input#newpass').val())) {
                if ($('input#pass').val() == $('input#newpass').val()) {
                    let newPass = $('input#pass').val();
                    $.post('./php/controllers/userController.php', {
                        action: 'changePassword',
                        id: this.id,
                        newPass: newPass
                    }, (msg) => {
                        if (json.status != 'ERROR') {
                            messageStore.commit('addMessage', {
                                text: 'Password changed',
                                type: 'success'
                            });
                        } else {
                            messageStore.commit('addMessage', {
                                text: 'Unable to change password',
                                type: 'error'
                            });
                        }
                    });
                } else {
                    messageStore.commit('addMessage', {
                        text: 'Passwords doesn\'t match',
                        type: 'warning'
                    });
                }
            } else {
                messageStore.commit('addMessage', {
                    text: 'Passwords cannot be empty',
                    type: 'warning'
                });
            }
        }
    }
};
</script>

<style media="screen">
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}

#img-upload{
    width: 150px;
    height: 150px;
    object-fit: cover;
}
</style>
