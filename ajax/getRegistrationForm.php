<script type="text/javascript">
<!--
  $(document).ready(function() {
          $('form#registration').submit(function() {
              $.ajax({
                  url: '/ajax/doRegister.php',
                  async: false,
                  data:  $('form#registration input').serialize()+'&web_version='+$.browser.version+'&web_browserclass='+($.browser.msie?'msie':($.browser.mozilla?'mozilla':($.browser.safari?'safari':($.browser.opera?'opera':'other')))),                   
                  dataType: 'json',
                  success: function(data) {
                      if (data.success) { 
                        if (data.authenticated==true) {
                          $_UID = data.id;
                          return false;
                        } else {
                          return false;
                        }
                      } else {
                      }
                    },
                  error: function() {
                      return false;
                    }
                });

              return false;
            });    
            
          $('.tb_remove').click(function() {
              self.parent.tb_remove();
              return false;
            });
    });
//-->
</script>
<form id="registration">
  <fieldset>
    <label for="o_Email">Email Address</label> <input id="o_Email" name="Email">
    <label for="o_Name">Display Name</label> <input id="o_Name" name="Name">
    <label for="o_Password">Password</label> <input id="o_Password" name="Password" type="password">
    <label for="o_VPassword">Verify Password</label> <input id="o_VPassword" name="VPassword" type="password">
  </fieldset>
  <input type="submit" value="Register">
  <input class="tb_remove" type="button" value="Close" />
</form>
