<?php
$this->addTplJSName([
    'jquery-chosen',
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
]);
$this->addTplCSSName(['jquery-ui', 'datatables', 'jquery-chosen']);
$perpage = !empty($filter['perpage']) ? (int)$filter['perpage'] : $options['perpage'];
?>

<form id="datagrid_filter">
    <?php if ($options['is_pagination']) { ?>
        <input type="hidden" name="page" value="1" />
        <input type="hidden" name="perpage" value="<?php echo $perpage; ?>" />
    <?php } ?>
    <input type="hidden" name="order_by" value="<?php echo isset($filter['order_by']) ? $filter['order_by'] : $options['order_by']; ?>" />
    <input type="hidden" name="order_to" value="<?php echo isset($filter['order_to']) ? $filter['order_to'] : $options['order_to']; ?>" />
    <?php foreach ($columns as $name => $column) { ?>
        <?php if (isset($column['filter'])) { ?>
            <?php echo html_input('hidden', $name, (isset($filter[$name]) ? $filter[$name] : '')); ?>
        <?php } ?>
    <?php } ?>
    <input type="hidden" id="advanced_filter" name="advanced_filter" value="" />
</form>

<form id="datagrid_form" action="" method="post"></form>

<?php if ($options['is_toolbar'] && $this->isToolbar()){ ?>
    <?php $this->toolbar('menu-toolbar'); ?>
<?php } ?>

<div class="position-relative dataTables_wrapper dt-bootstrap4 mb-4">
    <div class="table-responsive">
        <table id="datagrid" class="datagrid <?php if ($options['is_selectable']) { ?>datagrid_selectable<?php } ?> table table-striped table-bordered dataTable bg-white">
            <thead>
                <tr>
                    <?php foreach($columns as $name=>$column){ ?>
                        <?php if ($name === 'id' && !$options['show_id']){ $column['class'] = (isset($column['class']) ? $column['class'] : '').' d-none'; } ?>
                        <th rel="<?php echo $name; ?>" class="<?php if(!empty($column['class'])){ echo $column['class']; } ?><?php if($options['is_sortable']){ ?> sortable sorting<?php } ?>">
                            <?php echo isset($column['title']) ? $column['title'] : ''; ?>
                        </th>
                    <?php } ?>
                    <?php if($actions){ ?>
                        <th class="center" rel="dg_actions">
                            <?php echo LANG_CP_ACTIONS; ?>
                        </th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($options['is_filter']){ ?>
                <tr class="filter table-align-middle">
                    <?php foreach($columns as $name=>$column){ ?>
                        <?php if ($name === 'id' && !$options['show_id']){ $column['class'] = (isset($column['class']) ? $column['class'] : '').' d-none'; } ?>
                        <td class="p-2 <?php if(!empty($column['class'])){ ?><?php echo $column['class']; ?><?php } ?>">
                            <?php if (!empty($column['filter']) && $column['filter'] !== 'none'){ ?>
                                <?php $filter_attributes = !empty($column['filter_attributes']) ? $column['filter_attributes'] : []; ?>
                                <?php if(strpos($name, 'date_') === 0){ ?>

                                    <?php echo html_datepicker('filter_'.$name, (isset($filter[$name]) ? $filter[$name] : ''), array_merge($filter_attributes, ['id'=>'filter_'.$name, 'rel'=>$name, 'class' => 'input form-control-sm']), ['minDate'=>date(cmsConfig::get('date_format'), 86400)]); ?>

                                <?php } else { ?>
                                    <?php if (!empty($column['filter_select'])){ ?>

                                        <?php echo html_select('filter_'.$name, (is_array($column['filter_select']['items']) ? $column['filter_select']['items'] : $column['filter_select']['items']($name)), (isset($filter[$name]) ? $filter[$name] : ''), array_merge($filter_attributes, ['id'=>'filter_'.$name, 'rel'=>$name, 'class' => 'custom-select custom-select-sm'])); ?>

                                    <?php if (!empty($filter_attributes['multiple'])) { ?>
                                        <?php ob_start(); ?>
                                        <script>
                                            $('#filter_<?php echo $name; ?>').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', placeholder_text_multiple: '<?php echo LANG_SELECT_MULTIPLE; ?>', disable_search_threshold: 8, width: '100%', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>', search_contains: true, hide_results_on_select: false});
                                        </script>
                                        <?php $this->addBottom(ob_get_clean()); ?>
                                    <?php } ?>

                                    <?php } else if(!empty($column['filter_checkbox'])) { ?>
                                        <div class="custom-control custom-switch">
                                            <input type="checkbox" class="form-check-input input-checkbox custom-control-input" name="filter_<?php echo $name; ?>" value="1" id="filter_<?php echo $name; ?>" rel="<?php echo $name; ?>">
                                            <label class="custom-control-label" for="filter_<?php echo $name; ?>"><?php echo $column['filter_checkbox']; ?></label>
                                        </div>
                                    <?php } else { ?>

                                        <?php echo html_input('search', 'filter_'.$name, (isset($filter[$name]) ? $filter[$name] : ''), array_merge($filter_attributes, ['id'=>'filter_'.$name, 'rel'=>$name, 'class' => 'form-control-sm'])); ?>

                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        </td>
                    <?php } ?>
                    <?php if ($actions) { ?>
                        <td class="text-right">
                            &nbsp;
                        </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php if ($options['is_pagination'] || $options['is_selectable']){ ?>
    <div class="row">
        <div class="col-auto col-lg-5 d-flex">
            <div class="datagrid_pagination mr-2"></div>
            <div class="dataTables_length datagrid_resize">
                <label>
                    <small class="text-muted mr-2"><?php echo LANG_PAGES_SHOW_PERPAGE; ?></small>
                    <select class="custom-select custom-select-sm form-control form-control-sm">
                        <?php
                        $perpages = [15,30,50,100,200,500];
                        foreach($perpages as $p){ ?>
                            <option value="<?php echo $p; ?>"<?php if($p === $perpage){ ?> selected<?php } ?>><?php echo $p; ?></option>
                        <?php } ?>
                    </select>
                </label>
            </div>
        </div>
        <?php if ($options['is_selectable']){ ?>
            <div class="col col-lg-7 text-right">
                <div class="datagrid_navigation datagrid_select_actions">
                    <small class="shint text-muted mr-2"><?php echo LANG_CP_SELECT_HINT; ?></small>
                    <button type="button" class="btn btn-primary btn-sm sall"><?php echo LANG_SELECT_ALL; ?></button>
                    <button type="button" class="btn btn-warning btn-sm sremove"><?php echo LANG_DESELECT_ALL; ?></button>
                    <button type="button" class="btn btn-secondary btn-sm sinvert"><?php echo LANG_INVERT_ALL; ?></button>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
    <div class="datagrid_loading">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
</div>
<?php ob_start(); ?>
<script>
    <?php echo $this->getLangJS('LANG_LIST_EMPTY', 'LANG_LIST_NONE_SELECTED', 'LANG_PAGE_FIRST', 'LANG_PAGE_LAST'); ?>
    icms.datagrid.setOptions({
        url: '<?php echo $source_url; ?>',
        pages_count: 0,
        perpage: <?php echo $perpage; ?>,
        show_id: <?php echo intval($options['show_id']); ?>,
        is_sortable: <?php echo intval($options['is_sortable']); ?>,
        is_filter: <?php echo intval($options['is_filter']); ?>,
        is_draggable: <?php echo intval($options['is_draggable']); ?>,
        drag_save_url: '<?php echo $options['drag_save_url']; ?>',
        is_actions: <?php echo intval($options['is_actions']); ?>,
        is_pagination: <?php echo intval($options['is_pagination']); ?>,
        is_selectable: <?php echo intval($options['is_selectable']); ?>,
        order_by: '<?php echo isset($filter['order_by']) ? $filter['order_by'] : $options['order_by']; ?>',
        order_to: '<?php echo isset($filter['order_to']) ? $filter['order_to'] : $options['order_to']; ?>'
    });
    <?php if ($options['is_auto_init']){ ?>
        icms.datagrid.init();
    <?php } ?>
</script>
<?php $this->addBottom(ob_get_clean()); ?>