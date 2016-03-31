(function () {

  'use strict';

  angular.module('pdf', []).directive('ngPdf', function($window) {
    return {
      restrict: 'E',
      templateUrl: function(element, attr) {
        return attr.templateUrl ? attr.templateUrl : 'partials/viewer.html'
      },
      link: function (scope, element, attrs) {
        var url = scope.pdfUrl,
          pdfDoc = null,
          pageNum = 1,
          scale = (attrs.scale ? attrs.scale : 3),
          canvas = (attrs.canvasid ? document.getElementById(attrs.canvasid) : document.getElementById('pdf-canvas')),
          ctx = canvas.getContext('2d'),
          windowEl = angular.element($window);

        windowEl.on('scroll', function() {
          scope.$apply(function() {
            scope.scroll = windowEl[0].scrollY;
          });
        });

        PDFJS.disableWorker = true;
        scope.pageNum = pageNum;
        scope.scale = scale;

        scope.renderPage = function(num) {
          num = parseInt(num);

          pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport(scale);
//            var viewport = page.getViewport(canvas.width / page.getViewport(1.0).width);
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            var renderContext = {
              canvasContext: ctx,
              viewport: viewport
            };

            page.render(renderContext);

          });
        };

        scope.goPrevious = function() {
          if (scope.pageNum <= 1)
            return;
          scope.pageNum = parseInt(scope.pageNum, 10) - 1;
        };

        scope.goNext = function() {
          if (scope.pageNum >= pdfDoc.numPages)
            return;
          scope.pageNum = parseInt(scope.pageNum, 10) + 1;
        };

        scope.zoomIn = function() {
          scale = parseFloat(scale) + 0.2;
          scope.renderPage(scope.pageNum);
          return scale;
        };

        scope.zoomOut = function() {
          scale = parseFloat(scale) - 0.2;
          scope.renderPage(scope.pageNum);
          return scale;
        };

        scope.changePage = function() {
          scope.renderPage(scope.pageNum);
        };

        scope.rotate = function() {
          if (canvas.getAttribute('class') === 'rotate0') {
            canvas.setAttribute('class', 'rotate90');
          } else if (canvas.getAttribute('class') === 'rotate90') {
            canvas.setAttribute('class', 'rotate180');
          } else if (canvas.getAttribute('class') === 'rotate180') {
            canvas.setAttribute('class', 'rotate270');
          } else {
            canvas.setAttribute('class', 'rotate0');
          }
        };

        PDFJS.getDocument(url).then(function (_pdfDoc) {
          pdfDoc = _pdfDoc;
          scope.renderPage(scope.pageNum);

          scope.$apply(function() {
            scope.pageCount = _pdfDoc.numPages;
          });
        });

        scope.$watch('pageNum', function (newVal) {
          if (pdfDoc !== null)
            scope.renderPage(newVal);
        });

        /**
        scope.$watch('scale', function (newVal) {
          if (pdfDoc !== null) {
            scope.scale = scale = newVal;
            scope.renderPage(scope.pageNum);
          }
        });
        */
      }
    };
  });

})();
