jQuery(document).ready(function($){
	$($("#send_password")).closest('td').append('<br /><label for="auto_password_reset"><input type="checkbox" id="auto_password_reset" name="auto_password_reset" /> ' + new_user_password_reset_l10n.autoreset + '</label><input type="hidden" name="auto_password_reset_nonce" value="' + new_user_password_reset_l10n.nonce + '" />')
});
