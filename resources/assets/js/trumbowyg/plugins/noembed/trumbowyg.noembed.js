(function ($) {
    'use strict';

    var defaultOptions = {};

    $.extend(true, $.trumbowyg, {
        langs: {
            en: {
                noembed: 'Embed',
                noembedError: 'Error'
            }
        },

        plugins: {
            noembed: {
                init: function (trumbowyg) {
                    trumbowyg.o.plugins.noembed = $.extend(true, {}, defaultOptions, trumbowyg.o.plugins.noembed || {});

                    var btnDef = {
                        fn: function () {
                            var $modal = trumbowyg.openModalInsert(
                                // Title
                                trumbowyg.lang.noembed,

                                // Fields
                                {
                                    url: {
                                        label: 'URL',
                                        required: true
                                    },

                                    width: {
                                        label: 'Width (optional)'
                                    },

                                    height: {
                                        label: 'Height (optional)'
                                    },
                                },

                                // Callback
                                function (data) {
                                    trumbowyg.execCmd('insertHTML', createVideo(data.url, data.width ? data.width : 470, data.height ? data.height : 280));
                                    setTimeout(function () {
                                        trumbowyg.closeModal();
                                    }, 250);
                                }
                            );
                        }
                    };

                    trumbowyg.addBtnDef('noembed', btnDef);

                    function parseVideo(url) {
                        url.match(/(http:|https:|)\/\/(player.|www.)?(vimeo\.com|youtu(be\.com|\.be|be\.googleapis\.com))\/(video\/|embed\/|watch\?v=|v\/)?([A-Za-z0-9._%-]*)(\&\S+)?/);
                        var type = 'default';

                        if (RegExp.$3.indexOf('youtu') > -1) {
                            type = 'youtube';
                        } else if (RegExp.$3.indexOf('vimeo') > -1) {
                            type = 'vimeo';
                        }

                        return {
                            type: type,
                            src: RegExp.$6
                        };
                    }

                    function createVideo(url, width, height) {
                        var videoObj = parseVideo(url);

                        if (videoObj.type === 'default') {
                            var $video = $('<video />', {
                                class: 'video',
                                src: url,
                                controls: true,
                                width: width,
                                height: height
                            });

                            return $video[0].outerHTML;
                        }

                        var $iframe = $('<iframe />', {width: width, height: height});
                        $iframe.attr('frameborder', 0).attr('class', 'video').attr('data-type', videoObj.type);

                        if (videoObj.type == 'youtube') {
                            $iframe.attr('src', '//www.youtube.com/embed/' + videoObj.src).attr('data-id', videoObj.src);
                        } else if (videoObj.type == 'vimeo') {
                            $iframe.attr('src', '//player.vimeo.com/video/' + videoObj.src).attr('data-id', videoObj.src);
                        }

                        return $iframe[0].outerHTML;
                    }
                }
            }
        }
    });
})(jQuery);