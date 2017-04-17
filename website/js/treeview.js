// configure the tree
$('#council-tree').jstree(
{
	'core' : 
	{
		'data' : 
		{
			"url" : "/2017/SCO/council-tree.json",
			"dataType" : "json" // needed only if you do not supply JSON headers
		}
	},
	"types" : 
	{
		"#" : 
		{
		  "max_children" : 1,
		  "max_depth" : 4,
		  "valid_children" : ["root"]
		},
		"root" : 
		{
			"icon" : "/website/image/scotland-16.png",
			"valid_children" : ["council"]
		},
		"council" : 
		{
			"icon" : "/website/image/building-16.png",
			"valid_children" : ["ward"]
		},
		"ward" : 
		{
		  "icon" : "/website/image/group-16.png",
		  "valid_children" : ["candidate"]
		},
		"candidate" : 
		{
			"icon" : "/website/image/person-16.png",
			 "valid_children" : []
		}
	},
	"plugins" : ["types", "theme", "search"]
});


// search the tree
$(function () {
  $("#council-tree").jstree({
    "plugins" : [ "search" ]
  });
  var to = false;
  $('#council-tree-search').keyup(function () {
    if(to) { clearTimeout(to); }
    to = setTimeout(function () {
      var v = $('#council-tree-search').val();
      $('#council-tree').jstree(true).search(v);
    }, 250);
  });
});

// interaction and events
$('#evts_button').on("click", function () {
	var instance = $('#evts').jstree(true);
	instance.deselect_all();
	instance.select_node('1');
});
$('#evts')
	.on("changed.jstree", function (e, data) {
		if(data.selected.length) {
			alert('The selected node is: ' + data.instance.get_node(data.selected[0]).text);
		}
	})
	.jstree({
		'core' : {
			'multiple' : false,
			'data' : [
				{ "text" : "Root node", "children" : [
						{ "text" : "Child node 1", "id" : 1 },
						{ "text" : "Child node 2" }
				]}
			]
		}
	});