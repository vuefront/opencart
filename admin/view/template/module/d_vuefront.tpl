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
        <div class="module-content">
            <div class="module-content__image">
                <img src="view/image/d_vuefront/logo.png"/>
            </div>
            <div class="module-content__form">
                <h3 class="text-center mb-1"><?php echo $text_title; ?></h3>
                <input class="form-control mb-2" type="text" value="<?php echo $catalog; ?>" readonly>
                <p><?php echo $text_description; ?></p>
            </div>
        </div>
        <div style="text-align: center; padding: 30px;"><?php echo $text_powered_by; ?></div>
    </div>
  </div>
</div>
<style>
    .text-center {
        text-align: center;
    }
    .module-content__image {
        text-align: center;
    }
    .module-content__form {
        max-width: 45%;
        margin: 0 auto;
    }
    .module-content__form input{
        width: 100%;
    }
    .mb-1 {
            margin-bottom: 15px;
    }
    .mb-2 {
        margin-bottom: 25px;
    }
    @media(max-width: 767px) {
        .module-content__form {
            max-width: 100%;
        }
    }
</style>
<?php echo $footer; ?>
