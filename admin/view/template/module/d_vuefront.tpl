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
            <div class="module-content__image text-center">
                <img src="view/image/d_vuefront/logo.png"/>
            </div>
            <div class="module-content__form">
                <h3 class="text-center mb-1"><?php echo $text_title; ?></h3>
                <div class="input-group  mb-2">
                    <input id="connect_url" class="form-control" type="text" value="<?php echo $catalog; ?>" readonly>
                    <div class="input-group-btn">
                        <button class="clipboard" data-clipboard-target="#connect_url"><?php echo $text_copy; ?></button>
                    </div>
                </div>
                <p class="module-content__description"><?php echo $text_description; ?></p>
            </div>
        </div>
        <div style="text-align: center; padding: 30px;"><?php echo $text_powered_by; ?></div>
    </div>
  </div>
</div>
<style>
    .module-content
    {
        padding: 60px 0;
    }
    .module-content__form
    {
        max-width: 45%;
        margin: 0 auto;
    }
    .module-content__row
    {
        padding-top: 30px;
        padding-bottom: 30px;
    }
    .mb-1
    {
        margin-bottom: 15px;
    }
    .mb-2
    {
        margin-bottom: 25px;
    }
    @media(max-width: 767px)
    {
        .module-content__form
        {
            max-width: 100%;
        }
    }
    .module-content__description
    {
        font-size: 12px;
        line-height: 20px;

        padding-top: 20px;

        text-align: center;

        color: #777;
    }
    .module-content__image img
    {
        width: 200px;
    }
    .text-center
    {
        text-align: center;
    }

    .module-content__form input
    {
        width: 100%;
    }
    .mb-1
    {
        margin-bottom: 15px;
    }
    .mb-2
    {
        margin-bottom: 25px;
    }
    .input-group
    {
        position: relative;

        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;

        width: 100%;

        border-collapse: separate;

        -webkit-box-orient: horizontal;
        -webkit-box-direction: normal;
        -ms-flex-flow: row;
            flex-flow: row;
    }

    .input-group .form-control:first-child
    {
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;

        -webkit-box-flex: 1;
        -ms-flex: 1;
            flex: 1;
    }

    .input-group-btn
    {
        font-size: 0;

        position: relative;

        white-space: nowrap;
    }
    .input-group-addon,
    .input-group-btn
    {
        margin: 0;

        white-space: nowrap;
    }
    .input-group-addon button,
    .input-group-btn button
    {
        height: 100%;
    }
</style>
<script>
    
    var clipboard = new ClipboardJS('.clipboard');

    clipboard.on('success', function(e) {
        $(e.trigger).text('copied!');

        e.clearSelection();
    });
</script>
<?php echo $footer; ?>
