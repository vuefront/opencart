<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h1><i class="icon-puzzle"></i> <?php echo $heading_title; ?></h1>
      <div class="buttons">
        <a onclick="location = '<?php echo $cancel; ?>';" class="button"><?php echo $button_cancel; ?></a>
      </div>
    </div>
    <div class="content">
        <vf-app class="vuefront-app"></vf-app>
    </div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function (event) {
  d_vuefront({
    selector: '.vuefront-app',
    baseURL: '<?php echo $baseUrl; ?>',
    siteUrl: '<?php echo $siteUrl; ?>',
    tokenUrl: '<?php echo $tokenUrl; ?>',
    apiURL: '',
    type: 'opencart'
  })
})
</script>
<?php echo $footer; ?>
