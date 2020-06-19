<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/views/header.php';

$campaign_service = Campaign_Service::factory();
$campaigns = $campaign_service->get_campaigns($_SESSION['user_id']);

if (empty($_GET['id'])) {
    $camp_id = empty($campaigns) ? 0 : $campaigns[0]->c_id;
} else {
    $camp_id = $_GET['id'];
}

$size = 10;

?>

<link rel="stylesheet" href="/accets/laypage/skin/laypage.css" media="all">

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-9">
        <h2>Campaign Record List - <span id="span-campaign-name"></span></h2>
    </div>
    <div class="col-lg-3" style="margin-top: 20px">
        <h3>Total Records: <span id="span-total-records"></span></h3>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <br>
            <div class="ibox-title">
                <form id="form-campaign" method="GET" action="javascript:get_records();" class="form-inline">
                    <div class="form-group">
                        <strong>Campaigns: </strong>
                        <select name="camp_id" id="camp_id" class="form-control" onchange="this.form.submit()">
                            <?php
                            foreach ($campaigns as $item) {
                                $selected = ($item->c_id == $camp_id) ? 'selected="selected"' : '';
                                echo '<option value="' . $item->c_id . '"' . $selected . '> ' . $item->c_title . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div style="clear: both; height: 8px"></div>
                    <div class="form-group">
                        <input type="text" style="width: 400px; display:none;" name="search" id="search"
                            class="form-control" value="" />
                        <input type="submit" name="search" value="Search" class="btn btn-info"
                            style="margin-right: 20px; display:none;" />

                        <a id="btn-export" class="btn btn-info" href="javascript:;" target="_blank"
                            title="export">Export</a>
                    </div>

                    <div class="form-group pull-right">
                        <select name="size" id="size" class="form-control" onchange="this.form.submit()">
                            <option value="10" <?php echo $size == "10" ? 'selected="selected"' : ""; ?>>10</option>
                            <option value="20" <?php echo $size == "20" ? 'selected="selected"' : ""; ?>>20</option>
                            <option value="50" <?php echo $size == "50" ? 'selected="selected"' : ""; ?>>50</option>
                            <option value="100" <?php echo $size == "100" ? 'selected="selected"' : ""; ?>>100</option>
                        </select>
                    </div>
                </form>
            </div>

            <div class="ibox-content">
                <div class="table table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead id="thead-records">
                        </thead>
                        <tbody id="tbody-records">
                        </tbody>
                    </table>
                </div>

                <div id="div-pagination" style="margin-top: 10px; text-align: right;"></div>
            </div>
        </div>
    </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/views/footer.php'; ?>

<script src="/accets/laypage/laypage.js"></script>

<script type="text/javascript">
$(function() {
    $('#form-campaign').submit();
});

function get_records(page) {
    var option = $('#form-campaign #camp_id option:selected');
    if (option.length) {
        $('#span-campaign-name').text(option.text());
    }

    var camp_data = $('#form-campaign').serialize();
    var data = camp_data + '&page=' + (page || 1) + '&action=list';
    show_message('Loading...');

    $.post('./data.php', data, function(result) {
        if (result.message) {
            show_message(result.message);
        } else {
            render_campaign_data(result);
            render_table_head(result.data.columns);
            render_table_body(result.data.rows);
            render_pagination(result);
        }
    }).done(function() {
        set_export_href(camp_data);
    }).fail(function(xhr) {
        show_message(xhr.responseText || (xhr.status + ', ' + xhr.statusText));
    });
}

function show_message(message) {
    if (message) {
        var div = $('<h3 align="center" class="text-danger"/>').text(message);
        var td = $('<td colspan="100"/>').html(div);
        $('#tbody-records').html($('<tr/>').html(td));
    } else {
        $('#tbody-records').html('');
    }
}

function set_export_href(data) {
    $('#btn-export').attr('href', './data.php?' + data + '&action=export');
}

function render_campaign_data(data) {
    $('#span-total-records').text(data.total);
}

function render_table_head(columns) {
    $('#thead-records').html('');
    var tr = $('<tr/>').appendTo('#thead-records');

    $.each(columns, function(idx, item) {
        $('<th/>').text(item).appendTo(tr);
    });
}

function render_table_body(rows) {
    show_message('');

    if (!rows.length) {
        show_message('No record found');
        return;
    }

    $.each(rows, function(idx, row) {
        var tr = $('<tr/>').appendTo('#tbody-records');
        $.each(row, function(idx, item) {
            var value = Array.isArray(item) ? item.join('<br/>') : item;
            $('<td/>').html(value).appendTo(tr);
        });
    });
}

function render_pagination(data) {
    var pages = Math.ceil(data.total / data.size);
    laypage({
        cont: 'div-pagination',
        pages: pages,
        skin: 'yahei',
        curr: data.page,
        first: 1,
        last: pages,
        prev: '<',
        next: '>',
        groups: 3,
        jump: function(obj, first) {
            if (!first) {
                get_records(obj.curr);
            }
        }
    });
}
</script>