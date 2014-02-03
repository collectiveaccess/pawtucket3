<?php
/* ----------------------------------------------------------------------
 * views/Browse/browse_results_html.php : 
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2014 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 *
 * ----------------------------------------------------------------------
 */
 
	$qr_res 			= $this->getVar('result');				// browse results (subclass of SearchResult)
	$va_facets 			= $this->getVar('facets');				// array of available browse facets
	$va_criteria 		= $this->getVar('criteria');			// array of browse criteria
	$vs_browse_key 		= $this->getVar('key');					// cache key for current browse
	$va_access_values 	= $this->getVar('access_values');		// list of access values for this user
	$vn_hits_per_block 	= (int)$this->getVar('hits_per_block');	// number of hits to display per block
	$vn_start		 	= (int)$this->getVar('start');			// offset to seek to before outputting results
	
	$va_views			= $this->getVar('views');
	$vs_current_view	= $this->getVar('view');
	$va_view_icons		= $this->getVar('viewIcons');
	$vs_current_sort	= $this->getVar('sort');
	
	$vs_table 			= $this->getVar('table');
	$t_instance			= $this->getVar('t_instance');
	
	
	$va_options			= $this->getVar('options');
	$vs_extended_info_template = caGetOption('extendedInformationTemplate', $va_options, null);

	$vb_ajax			= (bool)$this->request->isAjax();
	
if (!$vb_ajax) {	// !ajax
?>
<div id='browseResults'>
	<div id="bViewButtons">
<?php
	foreach($va_views as $vs_view => $va_view_info) {
		if ($vs_current_view === $vs_view) {
			print '<a href="#" class="active"><span class="glyphicon '.$va_view_icons[$vs_view]['icon'].'"></span></a> ';
		} else {
			print caNavLink($this->request, '<span class="glyphicon '.$va_view_icons[$vs_view]['icon'].'"></span>', 'disabled', '*', '*', '*', array('view' => $vs_view, 'key' => $vs_browse_key)).' ';
		}
	}
?>
	</div>		
	<H1>
<?php 
		print _t('%1 %2 %3', $qr_res->numHits(), $t_instance->getProperty('NAME_SINGULAR'), ($qr_res->numHits() == 1) ? _t("Result") : _t("Results"));	
?>		
		<div class="btn-group">
			<i class="fa fa-gear bGear" data-toggle="dropdown"></i>
			<ul class="dropdown-menu" role="menu">
<?php
				if(is_array($va_sorts = $this->getVar('sortBy')) && sizeof($va_sorts)) {
					foreach($va_sorts as $vs_sort => $vs_sort_flds) {
						if ($vs_current_sort === $vs_sort) {
							print "<li><a href='#'><em>{$vs_sort}</em></a></li>\n";
						} else {
							print "<li>".caNavLink($this->request, $vs_sort, '', '*', '*', '*', array('view' => $vs_view, 'key' => $vs_browse_key, 'sort' => $vs_sort))."</li>\n";
						}
					}
				}
				
				if ((sizeof($va_criteria) > 0) && is_array($va_sorts) && sizeof($va_sorts)) {
?>
				<li class="divider"></li>
<?php
				}
				
				if (sizeof($va_criteria) > 0) {
					print "<li>".caNavLink($this->request, _t("Start Over"), '', '*', '*', '*', array('view' => $vs_view))."</li>";
				}	
?>
			</ul>
		</div><!-- end btn-group -->
	</H1>
	<div class="row" style="clear:both;">
		<div class='col-sm-8 col-md-9 col-lg-10'>
			<H2>
<?php
			if (sizeof($va_criteria) > 0) {
				$i = 0;
				foreach($va_criteria as $va_criterion) {
					print "<strong>".$va_criterion['facet'].':</strong> '.$va_criterion['value'].' ';
					print caNavLink($this->request, caGetThemeGraphic($this->request, 'buttons/x.png'), 'browseRemoveFacet', '*', '*', '*', array('removeCriterion' => $va_criterion['facet_name'], 'removeID' => $va_criterion['id'], 'view' => $vs_view, 'key' => $vs_browse_key));
					$i++;
					if($i < sizeof($va_criteria)){
						print ", ";
					}
				}
			}
?>		
			&nbsp;</H2>
			<div class="row">
				<div id="browseResultsContainer">
<?php
} // !ajax

print $this->render("Browse/browse_results_{$vs_current_view}_html.php");			

if (!$vb_ajax) {	// !ajax
?>
				</div><!-- end browseResultsContainer -->
			</div><!-- end row -->
		</div><!-- end col-10 -->
		<div class="col-sm-4 col-md-3 col-lg-2">
<?php
			print $this->render("Browse/browse_refine_subview_html.php");
?>			
		</div><!-- end col-2 -->
	</div><!-- end row -->
</div><!-- end browseResults -->	

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#browseResultsContainer').jscroll({
			autoTrigger: true,
			loadingHtml: "<?php print caBusyIndicatorIcon($this->request).' '.addslashes(_t('Loading...')); ?>",
			padding: 20,
			nextSelector: 'a.jscroll-next'
		});
	});
</script>
<?php
			print $this->render('Browse/browse_panel_subview_html.php');
} //!ajax
?>