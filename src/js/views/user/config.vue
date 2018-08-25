<template id="">
    <div class="well">
        <div class="panel panel-default">
            <div class="panel-heading"><h3>Change profile picture</h3></div>
            <div class="panel-body">
                <form enctype="multipart/form-data">
                    <div class="input-group">
                        <span class="input-group-btn">
                            <span class="btn btn-default btn-file">
                                <i class="glyphicon glyphicon-camera"></i> Upload image <input type="file" id="imgInp">
                            </span>
                        </span>
                        <input type="text" class="form-control" readonly>
                    </div>
                    <img id="img-upload" class="img-thumbnail"/>
                    <button type="button" name="button">
                        <i class="glyphicon glyphicon-floppy-disk"></i> 
                        Save profile picture
                    </button>
                    <button type="button" name="button">
                        <i class="glyphicon glyphicon-trash"></i>
                        Delete profile picture
                    </button>
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
    mounted: function() {
        $(document).ready( function() {
            $(document).on('change', '.btn-file :file', function() {
                var input = $(this),
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [label]);
            });

            $('.btn-file :file').on('fileselect', function(event, label) {

                var input = $(this).parents('.input-group').find(':text'),
                log = label;

                if( input.length ) {
                    input.val(log);
                } else {
                    if( log ) alert(log);
                }

            });
            function readURL(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $('#img-upload').attr('src', e.target.result);
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
        changePassword: function() {
            if (/([^\s])/.test($('input#pass').val().trim()) && /([^\s])/.test($('input#newpass').val())) {
                if ($('input#pass').val() == $('input#newpass').val()) {
                    let newPass = $('input#pass').val();
                    $.post('./php/controllers/userController.php', {
                        action: 'changePassword',
                        id: this.id,
                        newPass: newPass
                    }, (msg) => {
                        var json = JSON.parse(msg);
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
}
</style>
