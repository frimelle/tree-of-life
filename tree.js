$(function () {

	$('#tree_of_life').jstree({
		'core' : {
	  		'data' : {
	    		'url' : function (node) { return 'data.php' },
	    		'dataType': 'JSON',
    			'data' : function (node) {
        			return { 'entity_id' : node.id };
      			}
	  		},
	  		'themes' : {
	  			'icons' : false
	  		}
	  	}
	})
	
	.on('changed.jstree', function (e, data) {
	  	var i, j, r = [];
	    for(i = 0, j = data.selected.length; i < j; i++) {
	      r.push(data.instance.get_node(data.selected[i], true).find("a").attr("href"));
	    }
		    $("iframe").attr('src', r[0]);
	  });
});