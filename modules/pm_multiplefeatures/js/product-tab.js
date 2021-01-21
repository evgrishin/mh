function pmTransformSelect() {
	var pm_featureSelectList = $('div#product-tab-content-Features select[id^="feature_"][id$="_value"], div#product-features select[id^="feature_"][id$="_value"], div#step5 select[id^="feature_"][id$="_value"]');

	// At least one feature
	if (pm_featureSelectList.size() > 0) {
		pm_featureSelectList.each(function() {
			$select = $(this);
			$select.attr('multiple', 'multiple').prop('multiple', true);
			if ($select.attr('name').indexOf("[]") == -1) {
				$select.attr('name', $select.attr('name') + '[]');
			}
			id_feature = parseInt($select.attr('id').replace('feature_', '').replace('_value', ''));

			// Remove feature = 0
			$('option[value="0"]', $select).remove();

			// Set selected to option, and reorder them (add to the end for each selected feature)
			if (typeof(pm_FeatureList[id_feature]) != 'undefined' && pm_FeatureList[id_feature].length > 0)
				for (var key in pm_FeatureList[id_feature]) {
					if ($('option', $select).size() > 1) {
						$('option[value="' + pm_FeatureList[id_feature][key] + '"]', $select)
						.attr('selected', 'selected')
						.prop('selected', true)
						.detach()
						.insertAfter($('option:last-child', $select));
					} else {
						$('option[value="' + pm_FeatureList[id_feature][key] + '"]', $select)
						.attr('selected', 'selected')
						.prop('selected', true);
					}
				}
		});

		pm_featureSelectList.pmConnectedList({
			availableListTitle: pm_FeatureAvailableListTitle,
			selectedListTitle: pm_FeatureSelectedListTitle,
			addAllButtonLabel: pm_FeatureAddAllButtonLabel,
			removeAllButtonLabel: pm_FeatureRemoveAllButtonLabel,
			buttonClasses: 'btn btn-default',
		});
	}
}