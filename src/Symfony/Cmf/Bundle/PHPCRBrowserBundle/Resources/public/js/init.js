var AdminTree = (function () {

    var my = {};

    my.generateTreeStateArray = function (path) {

        var start, pos, segments, curSegment, curItem;

        curSegment = '';
        segments = [];
        start = 1;
        pos = path.indexOf('/', start);

        while (pos > 0) {
            curItem = path.substr(start, pos - start);
            if (curItem !== '') {
                curSegment = curSegment + '/' + curItem;
                segments.push(curSegment);
            }
            start = pos + 1;
            pos = path.indexOf('/', start);
        }

        curItem = path.substr(start);
        if (curItem !== '') {
            curSegment = curSegment + '/' + curItem;
            segments.push(curSegment);
        }
        return segments;
	};

    my.initTree = function () {

        jQuery(config.selector).jstree({
            "core": {
                "initially_load": config.path.expanded,
                "initially_open": config.path.preloaded
            },
            "plugins": [ "contextmenu", "themes", "types", "ui", "json_data" ],
            "json_data": {
                "ajax": {
                    url:    Routing.generate('symfony_cmf_phpcr_browser_children'),
                    data:   function (node) {
                        return { 'root' : jQuery(node).attr('id') };
                    }
                }
            },
            "types": {
                "max_depth":        -2,
                "max_children":     -2,
                "valid_children":  [ "folder" ],
                "types": {
                    "default": {
                        "valid_children": "none",
                        "icon": {
                            "image": config.icon.document
                        }
                    },
                    "folder": {
                        "valid_children": [ "default", "folder" ],
                        "icon": {
                            "image": config.icon.folder
                        }
                    }
                }
            },
            "contextmenu": {
                "items": {
                    "rename":   null,
                    "remove":   null,
                    "ccp":      null,
                    "create": {
                        "label":    "Create",
                        "submenu": config.doctypes,
                    },
                    "delete": {
                        "label":    "Delete",
                        "action":   function (node) {
                                        window.location = Routing.generate(config.routecollection[node.attr("className").replace(/\\/g, '')].routes.delete, { "id": node.attr("id") });
                                    }
                    }
                }
            }
        })
        .bind("select_node.jstree", function (event, data) {
            window.location = Routing.generate(config.routecollection[data.rslt.obj.attr("className").replace(/\\/g, '')].routes.edit, { "id": data.rslt.obj.attr("id") });
        })
        .delegate("a", "click", function (event, data) { event.preventDefault(); });
    };

	return my;

}());
