<?php namespace Views;

/**
 * Class CarouselView
 *
 * Shows information on the home UI, only using HTML5 and CSS
 * @author Thomas Cardon
 * @package Views
 */
class CarouselView extends View
{
    private $infos = array();

    public function add($title, $content, $type): void
    {
      $data;
      echo '<script>
        const initCanvas = id => window.addEventListener("load", ev => loadCanvas(id)); 

        async function loadCanvas(id) {
            const PDFJS = window["pdfjs-dist/build/pdf"];
            PDFJS.GlobalWorkerOptions.workerSrc = "//mozilla.github.io/pdf.js/build/pdf.worker.js";
            
            const canvas = document.getElementById(id);
            console.log(canvas.dataset.url);
            
            const loadingTask = PDFJS.getDocument(canvas.dataset.url);
            const pdf = await loadingTask.promise;
            
            const scale = 5.0;

            // Load information from the first page.
            const page = await pdf.getPage(1);
            let viewport = page.getViewport({ scale });

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const context = canvas.getContext("2d");
            
            // Render the page into the `<canvas>` element.
            const renderContext = {
                canvasContext: context,
                viewport,
            };
            await page.render(renderContext);
            console.log("Page rendered!");
            console.log("Chargement info #" + id);
        }
      </script>';
      
      if ($type !== 'text' && !file_exists(PATH . TV_UPLOAD_PATH . $content)) {
        $data = '
          <div class="py-5 text-center bg-warning bg-gradient w-100 h-100">
            <h1>Erreur</h1>
            <p class="lead">Fichier introuvable</p>
            <br />
            <p style="text-align: left;overflow-wrap: anywhere;margin: 2rem;">
              <samp>' . PATH . TV_UPLOAD_PATH . $content . '</samp>
            </p>
          </div>';
      }
      else {
        switch($type) {
          case 'pdf':
            $data = '
              <canvas id="' . $content . '" class="d-block w-100" data-url="' . BASE_URL . TV_UPLOAD_PATH . $content . '"></object>
              <script>initCanvas("' . $content . '");</script>  
            ';
            break;
          case 'text':
            $data = '
              <div class="py-5 text-center bg-info bg-gradient w-100 h-100">
                <p style="font-weight: 300;font-size: x-large;text-align: start;padding: 2rem;">' . strip_tags($content) . '</p>
              </div>';
            break;
          default:
            $data = '<img loading="lazy" src="' . URL_PATH . TV_UPLOAD_PATH . $content . '" class="d-block w-100 h-100">';
        }
      }
            
      array_push($this->infos, '
      <div class="carousel-item carousel-item-info' . (count($this->infos) == 0 ? ' active' : '') . '">
        ' . $data . '
        <div class="' . (strlen($title) > 32 ? 'marquee ' : '') . 'carousel-caption d-block bg-black opacity-75 text-white text-uppercase w-100 start-0 bottom-0 py-3 px-2" style="font-size: 1.15rem; white-space: nowrap; text-overflow:ellipsis;">
          <h5>' . $title . '</h5>
        </div>
      </div>');
    }

    public function build(): String
    {
        $indicators = '';
        
        for($i = 0; $i < count($this->infos); ++$i) {
          $indicators .= '<button type="button" data-bs-target="#informationCarousel" data-bs-slide-to="' . $i . '"' . ($i == 0 ? 'class="active" aria-current="true"' : '') . '></button>';
        }
        
        return '
        <div id="informationCarousel" class="carousel carousel-dark slide" data-bs-ride="carousel" data-bs-interval="20000" data-bs-touch="false" style="overflow: hidden;">
          <div class="carousel-indicators" style="top: 1px;">
            ' . $indicators . '
          </div>
          <div class="carousel-inner">
          ' . join('', $this->infos) . '
          </div>
        </div>';
    }
}
?>