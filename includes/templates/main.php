<?php
$res = new BaranApi(get_option('baranApiKey') ?? 'Baran3547');
$success = false;
$message = "";
if (isset($_POST['webservice']) && isset($_POST['website'])) {
    $webservice = strip_tags($_POST['webservice']);
    $website = strip_tags($_POST['website']);
    add_post_meta(intval($website), 'baranProductId', $webservice, true);
    $success = true;
    $message = "تنظیمات با موفقیت ذخیره شد !";
}
// Select Webservice Product Selected
$products = wooProducts();
$ids = [];
foreach ($products as $item) {
    if (isset(get_post_meta($item->get_id(), 'baranProductId')[0]) && is_numeric(get_post_meta($item->get_id(), 'baranProductId')[0])) {
        $ids[] = get_post_meta($item->get_id(), 'baranProductId')[0];
    }
}
?>


<div class="wrap">
    <h1>تنظیمات افزونه باران</h1>
    <?php settings_errors() ?>
    <?php if ($success) { ?>
        <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
            <p><strong><?= $message ?></strong></p>
            <button type="button" class="notice-dismiss">
                <span class="screen-reader-text">بستن این اطلاع.</span>
            </button>
        </div>
    <?php } ?>
    <div class="mywrapper">
        <div>
            <form action="options.php" method="post">
                <div id="dashboard-widgets-wrap">
                    <div id="dashboard-widgets" class="metabox-holder">
                        <div class="postbox-container" style="width: 100%">
                            <div id="normal-sortables" class="ui-sortable meta-box-sortable">
                                <!-- BOXES -->
                                <div class="postbox">
                                    <?php
                                    settings_fields('baran_options');
                                    do_settings_sections('baran');
                                    submit_button();
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="postbox-container" style="width: 100%;">
                        <div id="normal-sortables" class="ui-sortable meta-box-sortable">
                            <!-- BOXES -->
                            <form action="" method="post">
                                <div class="postbox">
                                    <h2>ارتباط محصولات</h2>
                                    <table class="form-table">
                                        <tbody>
                                        <tr>
                                            <th>محصولات نرم افزار باران</th>
                                            <td>
                                                <?php $datas = []; ?>
                                                <select class="myselectbox" style="width: 100%;" name="webservice">
                                                    <?php foreach ($res->products(1) as $product) { ?>
                                                        <?php
                                                        if (in_array($product->Code, $ids)) {
                                                            $datas[] = $product;
                                                            continue;
                                                        }
                                                        ?>
                                                        <option value="<?= $product->Code ?>"><?= $product->Name1 . ($product->Name2 ? '(' . $product->Name2 . ')' : '') . ' - ' . number_format($product->FinalPrice, 0) . ' تومان ' ?></option>
                                                    <?php } ?>
                                                    <?php if (count($datas)) { ?>
                                                        <optgroup label="انتخاب شده ها">
                                                            <?php foreach ($datas as $product) { ?>
                                                                <option value="<?= $product->Code ?>"><?= $product->Name1 . ($product->Name2 ? '(' . $product->Name2 . ')' : '') . ' - ' . number_format($product->FinalPrice, 0) . ' تومان ' ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>محصولات وبسایت</th>
                                            <td>
                                                <?php $datas = []; ?>
                                                <select class="myselectbox" style="width: 100%;" name="website">
                                                    <?php foreach ($products as $product) { ?>
                                                        <?php if (is_numeric(get_post_meta($product->get_id(), 'baranProductId')[0])) {
                                                            $datas[] = $product;
                                                            continue;
                                                        } ?>
                                                        <option value="<?= $product->get_id() ?>"><?= $product->get_title() ?></option>
                                                    <?php } ?>
                                                    <?php if (count($datas)) { ?>
                                                        <optgroup label="انتخاب شده ها">
                                                            <?php foreach ($datas as $product) { ?>
                                                                <option value="<?= $product->get_id() ?>"><?= $product->get_title() ?></option>
                                                            <?php } ?>
                                                        </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2" style="text-align: left;">
                                                <button type="submit" name="store" class="button button-primary">ذخیره
                                                    تغیرات
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="myownwrapper">
        <div>
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="postbox-container" style="width: 100%;">
                        <div id="normal-sortables" class="ui-sortable meta-box-sortable">
                            <!-- BOXES -->
                            <form action="" method="post">
                                <div class="postbox">
                                    <?php $logs = cronJobLogs(); ?>
                                    <h2>لیست بروزرسانی ها</h2>
                                    <?php if (count($logs)) { ?>
                                        <table class="form-table" border="1" style="text-align: center;">
                                            <thead>
                                            <th style="text-align: center;">شناسه</th>
                                            <th style="text-align: center;">زمان</th>
                                            <th style="text-align: center;">وضعیت</th>
                                            </thead>
                                            <tbody style="text-align: center">
                                            <?php foreach ($logs as $log) { ?>
                                                <tr>
                                                    <th style="text-align: center"><?= $log->id ?></th>
                                                    <td><?= date('Y/m/d H:i:s', $log->createdAt) ?></td>
                                                    <td style="color: <?= $log->success ? 'green' : 'red'; ?>"><?= $log->success ? 'موفق' : 'ناموفق' ?></td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    <?php } else { ?>
                                        <h3 style="text-align: center;color: red;margin-top: 15px;margin-bottom: 15px;font-weight: bold">
                                            هنوز هیچ بروزرسانی اجرا نشده</h3>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .mywrapper {
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    .mywrapper > div {
        width: calc(50% - 2rem);
        margin-left: 2rem;
    }

    .mywrapper > div:last-child {
        margin-left: 0rem;
    }

    .myownwrapper {
        padding: 0 15px;
    }

    .mywrapper > div .postbox, .myownwrapper > div .postbox {
        padding: 1rem 2rem;
    }

    .select2-container--default .select2-results__group {
        background-color: #e35b5b;
        color: #fff;
        font-weight: bold;
    }

    .select2-results__option select2-results__option--group ul {
        border: 2px solid #e35b5b !important;
    }

    table td, table th {
        padding: 5px;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet"/>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
<script>
    jQuery(document).ready(function () {
        jQuery('.myselectbox').select2();
    });
</script>