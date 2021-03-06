<?php 
require_once( 'core.php' ); 
require_once( 'core/bug_api.php' );
require_once( 'timetracking_api.php' ); 
layout_page_header( plugin_lang_get( 'title' ) ); 
layout_page_begin( plugin_page( 'show_report' ) );

$t_today = date( "d:m:Y" );
$t_date_submitted = isset( $t_bug ) ? date( "d:m:Y", $t_bug->date_submitted ) : '01:' . date( "m:Y" );
$t_plugin_TimeTracking_stats_from_def = $t_date_submitted;
$t_plugin_TimeTracking_stats_from_def_ar = explode ( ":", $t_plugin_TimeTracking_stats_from_def );
$t_plugin_TimeTracking_stats_from_def_d = $t_plugin_TimeTracking_stats_from_def_ar[0];
$t_plugin_TimeTracking_stats_from_def_m = $t_plugin_TimeTracking_stats_from_def_ar[1];
$t_plugin_TimeTracking_stats_from_def_y = $t_plugin_TimeTracking_stats_from_def_ar[2];
$t_plugin_TimeTracking_stats_from_d = gpc_get_int('start_day', $t_plugin_TimeTracking_stats_from_def_d);
$t_plugin_TimeTracking_stats_from_m = gpc_get_int('start_month', $t_plugin_TimeTracking_stats_from_def_m);
$t_plugin_TimeTracking_stats_from_y = gpc_get_int('start_year', $t_plugin_TimeTracking_stats_from_def_y);
$t_plugin_TimeTracking_stats_to_def = $t_today;
$t_plugin_TimeTracking_stats_to_def_ar = explode ( ":", $t_plugin_TimeTracking_stats_to_def );
$t_plugin_TimeTracking_stats_to_def_d = $t_plugin_TimeTracking_stats_to_def_ar[0];
$t_plugin_TimeTracking_stats_to_def_m = $t_plugin_TimeTracking_stats_to_def_ar[1];
$t_plugin_TimeTracking_stats_to_def_y = $t_plugin_TimeTracking_stats_to_def_ar[2];
$t_plugin_TimeTracking_stats_to_d = gpc_get_int('end_day', $t_plugin_TimeTracking_stats_to_def_d);
$t_plugin_TimeTracking_stats_to_m = gpc_get_int('end_month', $t_plugin_TimeTracking_stats_to_def_m);
$t_plugin_TimeTracking_stats_to_y = gpc_get_int('end_year', $t_plugin_TimeTracking_stats_to_def_y);
$f_plugin_TimeTracking_stats_button = gpc_get_string('plugin_TimeTracking_stats_button', '');
$f_project_id = helper_get_current_project();

		$t_collapse_block = is_collapsed( 'timefilter' );
		$t_block_css = $t_collapse_block ? 'collapsed' : '';
		$t_block_icon = $t_collapse_block ? 'fa-chevron-down' : 'fa-chevron-up';
?> 

<div class="col-md-12 col-xs-12 noprint">
	<div id="filter" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-filter"></i>
				<?php echo lang_get( 'filters' ) ?>
			</h4>
			<div class="widget-toolbar">
				<a id="filter-toggle" data-action="collapse" href="#">
					<i class="1 ace-icon fa <?php echo $t_block_icon ?> bigger-125"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<form method="post" action="<?php echo plugin_page( 'show_report' )?>">
				<div class="widget-main no-padding">

					<div class="table-responsive">

						<table class="width100" style="width: 100%" cellspacing="1">
							<tr class="row-category">
								<td class="category" width="10%"> &nbsp;
								</td> 
								<td class="category" colspan="2">
								<?php
								$t_filter = array();
								$t_filter['do_filter_by_date'] = 'on';
								$t_filter['start_day'] = $t_plugin_TimeTracking_stats_from_d;
								$t_filter['start_month'] = $t_plugin_TimeTracking_stats_from_m;
								$t_filter['start_year'] = $t_plugin_TimeTracking_stats_from_y;
								$t_filter['end_day'] = $t_plugin_TimeTracking_stats_to_d;
								$t_filter['end_month'] = $t_plugin_TimeTracking_stats_to_m;
								$t_filter['end_year'] = $t_plugin_TimeTracking_stats_to_y;
								filter_init( $t_filter );
								print_filter_do_filter_by_date(true);
								?>
								</td>
								<td class="category" colspan="2"> 
									<?php 
										$q_usr = db_query("SELECT id, username FROM " . db_get_table( 'mantis_user_table' )); 
										$all_users = array(); 
										while( $row = db_fetch_array( $q_usr ) ) { 
											$all_users[$row['id']] = $row['username']; 
										} 
										//print_r($all_users); 
									?> 
									<?php echo plugin_lang_get( 'select_user' ) ?>:  
									<select name="user_id"> 
										<option value="0"><?php echo plugin_lang_get( 'all_users' ) ?></option> 
										<?php foreach($all_users as $tmp_id => $tmp_user){ ?> 
											<option value="<?=$tmp_id?>" <?=$_POST['user_id']==$tmp_id ?  ' selected ':""?> > 
												<?=$tmp_user?> 
											</option> 
										<?php } ?> 
									</select> 
								</td> 

							</tr>
						<?php if ( access_has_global_level( plugin_config_get( 'view_others_threshold' ) ) ){ ?> 
							<tr class="row-2"> 
								<td class="category" width="10%"> &nbsp;
								</td> 
								<td class="category" width="20%"> 
									<input type="checkbox" value="Yes" name="txtDetailChk" <?php if(isset($_POST['txtDetailChk']) || !isset($_POST['user_id'])) echo "checked='checked'"; ?> />
									<?php echo plugin_lang_get( 'report_detail' ) ?> 
								</td> 
								<td class="category" width="20%"> 
									<input type="checkbox" value="Yes" name="txtUserChk" <?php if(isset($_POST['txtUserChk']) || !isset($_POST['user_id'])) echo "checked='checked'"; ?> />
									<?php echo plugin_lang_get( 'report_byuser' ) ?>
								</td> 
								<td class="category" width="20%"> 
									<input type="checkbox" value="Yes" name="txtProjectChk" <?php if(isset($_POST['txtProjectChk']) || !isset($_POST['user_id'])) echo "checked='checked'"; ?> />
									<?php echo plugin_lang_get( 'report_project' ) ?>
								</td> 
								<td class="category" width="20%"> 
									<input type="checkbox" value="Yes" name="txtIssueChk" <?php if(isset($_POST['txtIssueChk']) || !isset($_POST['user_id'])) echo "checked='checked'"; ?> />
									<?php echo plugin_lang_get( 'report_issue' ) ?>
								</td> 
							</tr> 
						<?php } ?> 
						</table>
					</div>
				</div>
				<div class="widget-toolbox padding-8 clearfix">
					<input type="submit" class="btn btn-primary btn-white btn-round" name="plugin_TimeTracking_stats_button" value="<?php echo plugin_lang_get( 'get_info' ) ?>" />
				</div>
			</form>
		</div>
	</div>

	<div class="space-10"></div>
<?php 

if ( !is_blank( $f_plugin_TimeTracking_stats_button ) ) {
	# Retrieve time tracking information
	$t_from = "$t_plugin_TimeTracking_stats_from_y-$t_plugin_TimeTracking_stats_from_m-$t_plugin_TimeTracking_stats_from_d";
	$t_to = "$t_plugin_TimeTracking_stats_to_y-$t_plugin_TimeTracking_stats_to_m-$t_plugin_TimeTracking_stats_to_d";
	$t_plugin_TimeTracking_stats = plugin_TimeTracking_stats_get_project_array( $f_project_id, $t_from, $t_to);
	//$t_sort_bug = $t_sort_name = array();
	//array_multisort( $t_sort_bug, SORT_NUMERIC, $t_sort_name, $t_plugin_TimeTracking_stats );
	//unset( $t_sort_bug, $t_sort_name );
?>

			<?php
			$t_sum_in_hours = 0;
			$t_user_summary = array();
			$t_project_summary = array();
			$t_bug_summary = array();
			# Initialize the user summary array
			foreach ( $t_plugin_TimeTracking_stats as $t_item ) {
			$t_user_summary[$t_item['username']] = 0;
			$t_project_summary[$t_item['project_name']] = 0;
			$t_bug_summary[$t_item['bug_id']] = 0;
			}
			foreach ( $t_plugin_TimeTracking_stats as $t_key => $t_item ) {
			$t_sum_in_hours += $t_item['hours'];
			$t_user_summary[$t_item['username']] += $t_item['hours'];
			$t_project_summary[$t_item['project_name']] += $t_item['hours'];
			$t_bug_summary[$t_item['bug_id']] += $t_item['hours'];
			}
			?>

	<?php if(isset($_POST['txtDetailChk'])){ ?>

	<div id="result" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-clock-o"></i>
				<?php echo plugin_lang_get( 'title' ) ?>
			</h4>
			<div class="widget-toolbar">
				<a id="result-toggle" data-action="collapse" href="#">
					<i class="1 ace-icon fa <?php echo $t_block_icon ?> bigger-125"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<div class="table-responsive">
			<table class="table table-bordered table-condensed table-hover table-striped" id="detailTable">
			<thead>
			<tr>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'user' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'expenditure_date' ) ?>
			</td>
			<td class="small-caption">
			<?php echo lang_get( 'issue_id' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'category' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'information' ) ?>
			</td>
			</tr>
			</thead>
			<tbody>
			<?php 			
				foreach ( $t_plugin_TimeTracking_stats as $t_key => $t_item ) { 
					$t_sum_in_hours += $t_item['hours'];
			?>
			<tr>
			<td class="small-caption">
			<?php echo $t_item['username'] ?>
			</td>
			<td class="small-caption">
			<?php echo date( config_get("short_date_format"), strtotime($t_item['expenditure_date'])) ?>
			</td>
			<td class="small-caption">
			<?php echo bug_format_summary( $t_item['bug_id'], SUMMARY_FIELD ) ?>
			</td>
			<td class="small-caption">
			<?php echo $t_item['category'] ?>
			</td>
			<td class="small-caption">
			<?php echo number_format($t_item['hours'], 2, '.', ',') ?> (<?php echo plugin_TimeTracking_hours_to_hhmm( $t_item['hours'] ); ?>)
			</td>
			<td class="small-caption">
			<?php echo $t_item['info'] ?>
			</td>
			</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr>
			<td class="small-caption">
			<?php echo lang_get( 'total_time' ); ?>
			</td>
			<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class="small-caption">
			<?php echo number_format($t_sum_in_hours, 2, '.', ','); ?> (<?php echo plugin_TimeTracking_hours_to_hhmm( $t_sum_in_hours ); ?>)
			</td><td>&nbsp;</td>
			</tr>
			</tfoot>
			</table>
			</div>
		</div>
	</div>
	
	<div class="space-10"></div>
	<script type="text/javascript" >
		
	</script>
	<?php } ?> 

	<?php if(isset($_POST['txtUserChk'])){ ?>
	<div id="result-user" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-clock-o"></i>
				<?php echo plugin_lang_get( 'title' ), ' - ', plugin_lang_get( 'user' ) ?>
			</h4>
			<div class="widget-toolbar">
				<a id="result-user-toggle" data-action="collapse" href="#">
					<i class="1 ace-icon fa <?php echo $t_block_icon ?> bigger-125"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<div class="table-responsive">
			<table class="table table-bordered table-condensed table-hover table-striped">
			<thead>
			<tr>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'user' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours_decimal' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours' ) ?>
			</td>
			</tr>
			</thead>

			<tbody>
			<?php 
			$t_sum_in_hours_user = 0;
			foreach ( $t_user_summary as $t_user_key => $t_user_value ) { 
				$t_sum_in_hours_user += $t_user_value;
			?>
			<tr <?php echo helper_alternate_class() ?>>
			<td class="small-caption">
			<?php echo lang_get( 'total_time' ); ?> - <?php echo $t_user_key; ?>
			</td>
			<td class="small-caption">
			<?php echo number_format($t_user_value, 2, '.', ','); ?>
			</td>
			<td class="small-caption">
				<?php echo plugin_TimeTracking_hours_to_hhmm( $t_user_value ); ?>
			</td>
			</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr>
			<td class="small-caption">
			<?php echo lang_get( 'total_time' ); ?>
			</td>
			<td class="small-caption">
			<?php echo number_format($t_sum_in_hours_user, 2, '.', ','); ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_TimeTracking_hours_to_hhmm( $t_sum_in_hours_user ); ?>
			</td>
			</tr>
			</tfoot>
			</table>
			</div>
		</div>
	</div>
	
	<div class="space-10"></div>
	<?php } ?> 

	<?php if(isset($_POST['txtProjectChk'])){ ?>
	<div id="result-project" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-clock-o"></i>
				<?php echo plugin_lang_get( 'title' ), ' - ', lang_get( 'project_name' ) ?>
			</h4>
			<div class="widget-toolbar">
				<a id="result-project-toggle" data-action="collapse" href="#">
					<i class="1 ace-icon fa <?php echo $t_block_icon ?> bigger-125"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<div class="table-responsive">
			<table class="table table-bordered table-condensed table-hover table-striped">
			<thead>
			<tr>
			<td class="small-caption">
			<?php echo lang_get( 'project_name' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours_decimal' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours' ) ?>
			</td>
			</tr>
			</thead>

			<tbody>
			<?php 
			$t_sum_in_hours_project = 0;
			foreach ( $t_project_summary as $t_project_key => $t_project_value ) { 
				$t_sum_in_hours_project += $t_project_value;	
			?>
			<tr <?php echo helper_alternate_class() ?>>
			<td class="small-caption">
			<?php echo $t_project_key; ?>
			</td>
			<td class="small-caption">
			<?php echo number_format($t_project_value, 2, '.', ','); ?>
			</td>
			<td class="small-caption">
				<?php echo plugin_TimeTracking_hours_to_hhmm( $t_project_value ); ?>
			</td>
			</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr>
			<td class="small-caption">
			<?php echo lang_get( 'total_time' ); ?>
			</td>
			<td class="small-caption">
			<?php echo number_format($t_sum_in_hours_project, 2, '.', ','); ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_TimeTracking_hours_to_hhmm( $t_sum_in_hours_project ); ?>
			</td>
			</tr>
			</tfoot>
			</table>
			</div>
		</div>
	</div>
	
	<div class="space-10"></div>
	<?php } ?> 

	<?php if(isset($_POST['txtIssueChk'])){ ?>
	<div id="result-issue" class="widget-box widget-color-blue2 <?php echo $t_block_css ?>">
		<div class="widget-header widget-header-small">
			<h4 class="widget-title lighter">
				<i class="ace-icon fa fa-clock-o"></i>
				<?php echo plugin_lang_get( 'title' ), ' - ', lang_get( 'issue_id' ) ?>
			</h4>
			<div class="widget-toolbar">
				<a id="esult-issue-toggle" data-action="collapse" href="#">
					<i class="1 ace-icon fa <?php echo $t_block_icon ?> bigger-125"></i>
				</a>
			</div>
		</div>

		<div class="widget-body">
			<div class="table-responsive">
			<table class="table table-bordered table-condensed table-hover table-striped">
			<thead>
			<tr>
			<td class="small-caption">
			<?php echo lang_get( 'issue_id' ) ?>
			</td>
			<td class="small-caption">
			Issue Description
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours_decimal' ) ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_lang_get( 'hours' ) ?>
			</td>
			</tr>
			</thead>
			<tbody>
			<?php 
			$t_sum_in_hours_bug = 0;
			foreach ( $t_bug_summary as $t_bug_key => $t_bug_value ) { 
				$t_sum_in_hours_bug += $t_bug_value;	
			?>
			<tr>
			<td class="small-caption">
			<?php echo bug_format_id( $t_bug_key ); ?>
			</td>
			<td class="small-caption">
			<?php echo bug_format_summary( $t_bug_key, SUMMARY_FIELD ) ?>
			</td>
			<td class="small-caption">
			<?php echo number_format($t_bug_value, 2, '.', ','); ?>
			</td>
			<td>
				<?php echo plugin_TimeTracking_hours_to_hhmm( $t_bug_value ); ?>
			</td>
			</tr>
			<?php } ?>
			</tbody>
			<tfoot>
			<tr>
			<td class="small-caption">
			<?php echo lang_get( 'total_time' ); ?>
			</td>
			<td class="small-caption">
			&nbsp;
			</td>
			<td class="small-caption">
			<?php echo number_format($t_sum_in_hours_bug, 2, '.', ','); ?>
			</td>
			<td class="small-caption">
			<?php echo plugin_TimeTracking_hours_to_hhmm( $t_sum_in_hours_bug ); ?>
			</td>
			</tr>
			</tfoot>
			</table>
			</div>
		</div>
	</div>
	<?php } ?> 

<?php } ?>
</div>
<?php
layout_page_end();
?>
