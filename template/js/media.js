jQuery(function($) {
	
	if ($(this).width() <= 768) {
		$('#main-content table').each(function() {
			var head = $(this).find('thead tr th').map(function(){
				return $.trim($(this).text());
			}).get();			
			if (head.length > 0) {
				$(this).addClass('mobile-table');
				$(this).find('tbody tr').each(function () {
					var tr = $(this);
					tr.find('td').each(function (i, e) {
						$('<td class="tr-head">' + head[i] + '</td>').insertBefore($(this));
					});
				});
			}
		});
	}
	
});