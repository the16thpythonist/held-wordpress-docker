
jQuery(document).ready(function () {
    var w = 700, h = 600;
    console.log('I am here');
    var labelDistance = 2;

    var vis = d3.select(".here").append("svg:svg").attr("width", w).attr("height", h);

    var labelAnchors = [];
    var labelAnchorLinks = [];

    // Getting the nodes and the links from the server via ajax;
    var nodes = getHelmholtzJSON('author-nodes');
    console.log(nodes);
    var links = getHelmholtzJSON('author-links');

    nodes.forEach(function (node) {
       labelAnchors.push({
           node: node
       });
        labelAnchors.push({
            node: node
        });
    });

    /*
    for(var i = 0; i < 30; i++) {
        var node = {
            label : "node " + i
        };
        nodes.push(node);
        labelAnchors.push({
            node : node
        });
        labelAnchors.push({
            node : node
        });
    };
    */


    nodes.forEach(function (node) {
       labelAnchorLinks.push({
           source: node['index'] * 2,
           target: node['index'] * 2 + 1,
           weight: 1
       })
    });
    /*
    for(var i = 0; i < nodes.length; i++) {
        for(var j = 0; j < i; j++) { if(Math.random() > .95)
            links.push({
                source : i,
                target : j,
                weight : Math.random()
            });
        }
        labelAnchorLinks.push({
            source : i * 2,
            target : i * 2 + 1,
            weight : 3
        });
    };
    */

    var force = d3.layout.force().size([w, h]).nodes(nodes).links(links).gravity(1).linkDistance(50).charge(-3000).linkStrength(function(x) {
        return x.weight * 10
    });


    force.start();

    var force2 = d3.layout.force().nodes(labelAnchors).links(labelAnchorLinks).gravity(0).linkDistance(10).linkStrength(8).charge(-300/*-100*/).size([w, h]);
    force2.start();

    var link = vis.selectAll("line.link").data(links).enter().append("svg:line").attr("class", "link").style("stroke", "#CCC");

    function lol(item) {
        //console.log(item);
        return item['radius'];
    }

    var node = vis.selectAll("g.node").data(force.nodes()).enter().append("svg:g").attr("class", "node");
    node.append("svg:circle").attr("r", lol).style("fill", "rgba(0, 0, 0, 0.6)").style("stroke", "#FFF").style("stroke-width", 3);
    node.call(force.drag);

    var anchorLink = vis.selectAll("line.anchorLink").data(labelAnchorLinks)//.enter().append("svg:line").attr("class", "anchorLink").style("stroke", "#999");

    var anchorNode = vis.selectAll("g.anchorNode").data(force2.nodes()).enter().append("svg:g").attr("class", "anchorNode");
    anchorNode.append("svg:circle").attr("r", 0).style("fill", "#FFF");
    anchorNode.append("svg:text").text(function(d, i) {
        return i % 2 == 0 ? "" : d.node.label
    }).style("fill", "#444").style("font-family", "Arial").style("font-size", 10);

    var updateLink = function() {
        this.attr("x1", function(d) {
            return d.source.x;
        }).attr("y1", function(d) {
            return d.source.y;
        }).attr("x2", function(d) {
            return d.target.x;
        }).attr("y2", function(d) {
            return d.target.y;
        });

    }

    var updateNode = function() {
        this.attr("transform", function(d) {
            return "translate(" + d.x + "," + d.y + ")";
        });

    }


    force.on("tick", function() {

        force2.start();

        node.call(updateNode);

        anchorNode.each(function(d, i) {
            if(i % 2 == 0) {
                d.x = d.node.x;
                d.y = d.node.y;
            } else {
                var b = this.childNodes[1].getBBox();

                var diffX = d.x - d.node.x;
                var diffY = d.y - d.node.y;

                var dist = Math.sqrt(diffX * diffX + diffY * diffY);

                var shiftX = b.width * (diffX - dist) / (dist * 2);
                shiftX = Math.max(-b.width, Math.min(0, shiftX));
                var shiftY = 5;
                this.childNodes[1].setAttribute("transform", "translate(" + shiftX + "," + shiftY + ")");
            }
        });


        anchorNode.call(updateNode);

        link.call(updateLink);
        anchorLink.call(updateLink);
    });
});