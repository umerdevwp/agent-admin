   <div class="sidebar scroller">
        <div class="panel">
          <div class="panel-header">
            <div class="panel-title"><span class="panel-icon fa-trophy"></span><span>Right Sidebar Content</span></div>
          </div>
          <div class="panel-body">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce volutpat ac tortor eu viverra. Etiam ipsum neque, fermentum quis sagittis nec, hendrerit id diam. Mauris a tincidunt odio. Sed porttitor ex pulvinar, tristique sapien sed, malesuada nunc.</p>
          </div>
        </div>
      </div>
     <div class="template-panel">
        <div class="template-panel-heading">Preferences
          <button class="template-panel-switch fa-cog" data-multi-switch='{"targets":".template-panel","scope":".template-panel","isolate":"[data-multi-switch]"}'></button>
        </div>
        <div class="template-panel-body">
          <div class="template-panel-group">
            <button class="theme-switch fa-check" title="Navbar Theme" style="border-color: #b5b5b5 #b5b5b5 #4b4a4a #4b4a4a; color: white;" data-theme-switch="concrete-navbar-default" data-theme='{"navbar-color":"#fff","navbar-bg":"#3d3c3c","navbar-hover-color":"#fff","navbar-hover-bg":"#fff","navbar-title-color":"#adb5bd","navbar-panel-bg":"#b5b5b5","navbar-brand-invert":"0%"}'></button>
            <button class="theme-switch fa-check" title="Navbar Theme" style="border-color: #29d999 #29d999 #1a1f21 #1a1f21; color: white;" data-theme-switch="concrete-navbar-success" data-theme='{"navbar-color":"#fff","navbar-bg":"#1a1f21","navbar-hover-color":"#fff","navbar-hover-bg":"#ededed","navbar-title-color":"#adb5bd","navbar-panel-bg":"#29d999","navbar-brand-invert":"0%"}'></button>
            <button class="theme-switch fa-check" title="Navbar Theme" style="border-color: #3e84d7 #3e84d7 #1a1f21 #1a1f21; color: white;" data-theme-switch="concrete-navbar-primary" data-theme='{"navbar-color":"#fff","navbar-bg":"#1a1f21","navbar-hover-color":"#fff","navbar-hover-bg":"#ededed","navbar-title-color":"#adb5bd","navbar-panel-bg":"#3e84d7","navbar-brand-invert":"0%"}'></button>
            <button class="theme-switch fa-check" title="Navbar Theme" style="border-color: #fff #fff #666 #666; color: black;" data-theme-switch="concrete-navbar-light" data-theme='{"navbar-color":"#666","navbar-bg":"#efefef","navbar-hover-color":"#333","navbar-hover-bg":"#333","navbar-title-color":"#333","navbar-panel-bg":"#eee","navbar-brand-invert":"100%"}'></button>
          </div>
          <!-- <ul class="template-panel-list">
            <ul class="template-panel-item"><a class="template-panel-link" href="../admindex/index.html">Admindex<span class="template-panel-popup"><img class="template-panel-preview" src="images/templates/template-01-400x174.png"></span></a></ul>
            <ul class="template-panel-item"><a class="template-panel-link" href="../adminifix/index.html">Adminifix<span class="template-panel-popup"><img class="template-panel-preview" src="images/templates/template-02-400x174.png"></span></a></ul>
            <ul class="template-panel-item"><a class="template-panel-link" href="../emerald/index.html">Emerald<span class="template-panel-popup"><img class="template-panel-preview" src="images/templates/template-03-400x174.png"></span></a></ul>
            <ul class="template-panel-item"><a class="template-panel-link" href="../iodashboard/index.html">IoDashboard<span class="template-panel-popup"><img class="template-panel-preview" src="images/templates/template-04-400x174.png"></span></a></ul>
            <ul class="template-panel-item"><a class="template-panel-link active" href="../concrete/index.html">Concrete<span class="template-panel-popup"><img class="template-panel-preview" src="images/templates/template-05-400x174.png"></span></a></ul>
          </ul> -->
        </div>
      </div>
    </div> 
</ul></nav></div></div></div>
<div id="divPositiveSSL">
<script type="text/javascript"> //<![CDATA[
var tlJsHost = ((window.location.protocol == "https:") ? "https://secure.trust-provider.com/" : "http://www.trustlogo.com/");
document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
//]]></script>
<script language="JavaScript" type="text/javascript">
TrustLogo("https://www.positivessl.com/images/seals/positivessl_trust_seal_sm_124x32.png", "POSDV", "none");
</script>
</div>
</body>
</html>
<script src="components/base/script.js"></script>
<script src="components/base/jquery-3.4.1.min.js"></script>
<script src="components/base/moment.min.js"></script>
<script src="components/datepicker/bootstrap-datepicker.js"></script>
<script>
$(document).ready(function(){
  $('.template-panel-switch').on('click', function(){
    $('.template-panel').toggleClass('active');
  });
  $('.add-contact').click(function(){
    $('#addMultiple').modal();
  });
  $('#inputFormationDate').datepicker();
  $('button.theme-switch').each(function(num,obj){

    $(obj).click(function(){
        $.ajax({
          type:"POST",
          url:"ajax/theme/save/",
          data:{ number: num },
          success: function(msg){
            // already saving data, no need to show modal
          }
        });
      });
  });
  
  getTheme();
  
});

function getTheme()
{
  $.ajax({
          type:"GET",
          url:"ajax/theme/name",
          success: function(msg){
            var obj = JSON.parse(msg);
            $("button.theme-switch").eq(obj.ok).addClass("active");
          }
        });
}


</script>