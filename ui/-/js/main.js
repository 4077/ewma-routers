(function (__nodeNs__, __nodeId__) {
    $.widget(__nodeNs__ + "." + __nodeId__, {
        options: {},

        _create: function () {
            var widget = this;
            var $widget = this.element;
            var options = widget.options;

            var $routers = $("> .routers", $widget);
            var $router = $("> .router", $widget);

            $routers.scrollLeft(options.viewports.routers.scroll[0]).scrollTop(options.viewports.routers.scroll[1]);
            $router.scrollLeft(options.viewports.router.scroll[0]).scrollTop(options.viewports.router.scroll[1]);

            var scrollTimeout;

            $routers.rebind("scroll." + __nodeId__, function () {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }

                scrollTimeout = setTimeout(function () {
                    request(options.paths.updateViewport, {
                        viewport: 'routers',
                        scroll:   {
                            top:  $routers.scrollTop(),
                            left: $routers.scrollLeft()
                        }
                    }, null, true);
                }, 400);
            });

            $router.rebind("scroll." + __nodeId__, function () {
                if (scrollTimeout) {
                    clearTimeout(scrollTimeout);
                }

                scrollTimeout = setTimeout(function () {
                    request(options.paths.updateViewport, {
                        viewport: 'router',
                        scroll:   {
                            top:  $router.scrollTop(),
                            left: $router.scrollLeft()
                        }
                    }, null, true);
                }, 400);
            });
        }
    });
})(__nodeNs__, __nodeId__);
