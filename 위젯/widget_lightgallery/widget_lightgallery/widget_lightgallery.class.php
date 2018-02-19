<?php
    /**
     * @class widget_lightgallery
     * @author XENARA (kolaskks@naver.com)
     * @version 0.1
     **/

 


    class widget_lightgallery extends WidgetHandler {

        function proc($args) {

          if($args->module_srl){
            $oModuleModel = &getModel('module');
            $oDocumentModel = &getModel('document');

            /* LightGallery 라이브러리 인클루드 */
    			  Context::addCSSFile($this->widget_path.'lightGallery/css/lightgallery.css');
    			  Context::addCSSFile($this->widget_path.'lightGallery/css/lg-transitions.css');
    			  Context::addCSSFile($this->widget_path.'lightGallery/css/lg-fb-comment-box.css');
    			  Context::addJSFile('https://cdn.jsdelivr.net/picturefill/2.3.1/picturefill.min.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lightgallery.min.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-fullscreen.min.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-thumbnail.min.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-video.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-autoplay.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-zoom.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-hash.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/lg-pager.js');
    			  Context::addJSFile($this->widget_path.'lightGallery/js/jquery.mousewheel.min.js');


            /* 반응형 설정 */
            if($args->use_response){
              $gallery_obj->use_response = $args->use_response;
            } else{
              $gallery_obj->use_response = 'Y';
            }
            if($args->response_width){
              $gallery_obj->response_width = $args->response_width;
            } else{
              $gallery_obj->response_width = 479;
            }

            /* 목록 설정 */
            $gallery_obj->module_srl = $args->module_srl;
            if($args->category_srl){
              $gallery_obj->category_srl = $args->category_srl;
            }
            if($args->member_srl){
              $gallery_obj->member_srl = $args->member_srl;
            }
            if($args->document_type=='notice'){
              $gallery_obj->is_notice = 'Y';
            } else if($args->document_type=='all'){
            } else{
              $gallery_obj->is_notice = 'N';
            }
            if($args->list_count){
              $gallery_obj->list_count = $args->list_count;
            } else{
              $gallery_obj->list_count = 20;
            }
            if($args->sort_index){
              $gallery_obj->sort_index = $args->sort_index;
            } else{
              $gallery_obj->sort_index = "regdate";
            }
            if($args->order_type){
              $gallery_obj->order_type = $args->order_type;
            } else{
              $gallery_obj->order_type = "desc";
            }
            if(Context::get('page')){
              $gallery_obj->page = Context::get('page');
            } else{
              $gallery_obj->page = 1;
            }
            $gallery_output = executeQueryArray('widgets.widget_lightgallery.getDocumentGalleryList',$gallery_obj);
            $gallery_list = $gallery_output->data;
            $gallery_total_count = $gallery_output->total_count;
            $gallery_total_page_count = $gallery_output->total_page_count;
            $galley_page_navigation = $gallery_output->page_navigation;
            //무작위 출력 설정
            if($args->shuffle_type=='Y'){
              shuffle($gallery_list);
            }
            //캡션 출력유무
            if($args->output_caption){
              $gallery_obj->output_caption = $args->output_caption;
            } else{
              $gallery_obj->output_caption = 'Y';
            }
            //링크 타겟
            if($args->link_target){
              $gallery_obj->link_target = $args->link_target;
            } else{
              $gallery_obj->link_target = '_self';
            }
            //첨부이미지 출력방법
            if($args->image_output_type){
              $gallery_obj->image_output_type = $args->image_output_type;
            } else{
              $gallery_obj->image_output_type = "first";
            }

            /* 썸네일 설정 */
            if($args->thumbnail_width){
              $gallery_obj->thumbnail_width = $args->thumbnail_width;
            } else{
              $gallery_obj->thumbnail_width = 225;
            }
            if($args->thumbnail_height){
              $gallery_obj->thumbnail_height = $args->thumbnail_height;
            } else{
              $gallery_obj->thumbnail_height = 150;
            }
            if($args->thumbnail_type){
              $gallery_obj->thumbnail_type = $args->thumbnail_type;
            } else{
              $gallery_obj->thumbnail_type = 'crop';
            }

            /* Light Gallery 옵션 설정 */
            if($args->mode){
              $gallery_obj->mode = $args->mode;
            } else{
              $gallery_obj->mode = 'lg-slide';
            }
            if($args->csseasing){
              $gallery_obj->csseasing = $args->csseasing;
            } else{
              $gallery_obj->csseasing = 'cubic-bezier(0.250, 0.100, 0.250, 1.000)';
            }
            if($args->speed){
              $gallery_obj->speed = $args->speed;
            } else{
              $gallery_obj->speed = 600;
            }
            if($args->backdropduration){
              $gallery_obj->backdropduration = $args->backdropduration;
            } else{
              $gallery_obj->backdropduration = 150;
            }
            if($args->hidebarsdelay){
              $gallery_obj->hidebarsdelay = $args->hidebarsdelay;
            } else{
              $gallery_obj->hidebarsdelay = 6000;
            }
            if($args->useleft){
              $gallery_obj->useleft = $args->useleft;
            } else{
              $gallery_obj->useleft = 'false';
            }
            if($args->closable){
              $gallery_obj->closable = $args->closable;
            } else{
              $gallery_obj->closable = 'true';
            }
            if($args->loop){
              $gallery_obj->loop = $args->loop;
            } else{
              $gallery_obj->loop = 'true';
            }
            if($args->esckey){
              $gallery_obj->esckey = $args->esckey;
            } else{
              $gallery_obj->esckey = 'true';
            }
            if($args->keypress){
              $gallery_obj->keypress = $args->keypress;
            } else{
              $gallery_obj->keypress = 'true';
            }
            if($args->controls){
              $gallery_obj->controls = $args->controls;
            } else{
              $gallery_obj->controls = 'true';
            }
            if($args->slideendanimatoin){
              $gallery_obj->slideendanimatoin = $args->slideendanimatoin;
            } else{
              $gallery_obj->slideendanimatoin = 'true';
            }
            if($args->hidecontrolonend){
              $gallery_obj->hidecontrolonend = $args->hidecontrolonend;
            } else{
              $gallery_obj->hidecontrolonend = 'false';
            }
            if($args->mousewheel){
              $gallery_obj->mousewheel = $args->mousewheel;
            } else{
              $gallery_obj->mousewheel = 'true';
            }
            if($args->getcaptionfromtitleoralt){
              $gallery_obj->getcaptionfromtitleoralt = $args->getcaptionfromtitleoralt;
            } else{
              $gallery_obj->getcaptionfromtitleoralt = 'true';
            }
            if($args->showafterload){
              $gallery_obj->showafterload = $args->showafterload;
            } else{
              $gallery_obj->showafterload = 'true';
            }
            if($args->download){
              $gallery_obj->download = $args->download;
            } else{
              $gallery_obj->download = 'true';
            }
            if($args->counter){
              $gallery_obj->counter = $args->counter;
            } else{
              $gallery_obj->counter = 'true';
            }
            if($args->swipethreshold){
              $gallery_obj->swipethreshold = $args->swipethreshold;
            } else{
              $gallery_obj->swipethreshold = 50;
            }
            if($args->enabledrag){
              $gallery_obj->enabledrag = $args->enabledrag;
            } else{
              $gallery_obj->enabledrag = 'true';
            }
            if($args->enabletouch){
              $gallery_obj->enabletouch = $args->enabletouch;
            } else{
              $gallery_obj->enabletouch = 'true';
            }

            /* Light Gallery Thumbnail 옵션 설정 */
            if($args->thumbnail){
              $gallery_obj->thumbnail = $args->thumbnail;
            } else{
              $gallery_obj->thumbnail = 'true';
            }
            if($args->animatethumb){
              $gallery_obj->animatethumb = $args->animatethumb;
            } else{
              $gallery_obj->animatethumb = 'true';
            }
            if($args->currentpagerposition){
              $gallery_obj->currentpagerposition = $args->currentpagerposition;
            } else{
              $gallery_obj->currentpagerposition = 'middle';
            }
            if($args->thumbwidth){
              $gallery_obj->thumbwidth = $args->thumbwidth;
            } else{
              $gallery_obj->thumbwidth = 100;
            }
            if($args->thumbcontheight){
              $gallery_obj->thumbcontheight = $args->thumbcontheight;
            } else{
              $gallery_obj->thumbcontheight = 100;
            }
            if($args->thumbmargin){
              $gallery_obj->thumbmargin = $args->thumbmargin;
            } else{
              $gallery_obj->thumbmargin = 5;
            }
            if($args->tooglethumb){
              $gallery_obj->tooglethumb = $args->tooglethumb;
            } else{
              $gallery_obj->tooglethumb = 'true';
            }
            if($args->pullcaptionup){
              $gallery_obj->pullcaptionup = $args->pullcaptionup;
            } else{
              $gallery_obj->pullcaptionup = 'true';
            }
            if($args->enablethumbdrag){
              $gallery_obj->enablethumbdrag = $args->enablethumbdrag;
            } else{
              $gallery_obj->enablethumbdrag = 'true';
            }
            if($args->enablethumbswipe){
              $gallery_obj->enablethumbswipe = $args->enablethumbswipe;
            } else{
              $gallery_obj->enablethumbswipe = 'true';
            }

            /* Light Gallery Autoplay 옵션 설정 */
            if($args->autoplay){
              $gallery_obj->autoplay = $args->autoplay;
            } else{
              $gallery_obj->autoplay = 'true';
            }
            if($args->pause){
              $gallery_obj->pause = $args->pause;
            } else{
              $gallery_obj->pause = 5000;
            }
            if($args->progressbar){
              $gallery_obj->progressbar = $args->progressbar;
            } else{
              $gallery_obj->progressbar = 'true';
            }
            if($args->fourceautoplay){
              $gallery_obj->fourceautoplay = $args->fourceautoplay;
            } else{
              $gallery_obj->fourceautoplay = 'false';
            }
            if($args->autoplaycontrols){
              $gallery_obj->autoplaycontrols = $args->autoplaycontrols;
            } else{
              $gallery_obj->autoplaycontrols = 'true';
            }

            /* Light Gallery 기타 옵션 설정 */
            if($args->fullscreen){
              $gallery_obj->fullscreen = $args->fullscreen;
            } else{
              $gallery_obj->fullscreen = 'true';
            }
            if($args->pager){
              $gallery_obj->pager = $args->pager;
            } else{
              $gallery_obj->pager = 'false';
            }
            if($args->zoom){
              $gallery_obj->zoom = $args->zoom;
            } else{
              $gallery_obj->zoom = 'true';
            }
            if($args->scale){
              $gallery_obj->scale = $args->scale;
            } else{
              $gallery_obj->scale = 1;
            }
            if($args->enablezoomafter){
              $gallery_obj->enablezoomafter = $args->enablezoomafter;
            } else{
              $gallery_obj->enablezoomafter = 50;
            }
            if($args->actualsize){
              $gallery_obj->actualsize = $args->actualsize;
            } else{
              $gallery_obj->actualsize = 'true';
            }


            /* 스킨 설정 */
            $gallery_obj->skin = $args->skin; //스킨명 변수설정


            /* 템플릿 변수 설정 */
            Context::set('oModuleModel', $oModuleModel);
            Context::set('oDocumentModel', $oDocumentModel);
            Context::set('colorset', $args->colorset);
            Context::set('gallery_obj', $gallery_obj);
            Context::set('gallery_list', $gallery_list);
            Context::set('gallery_total_count', $gallery_total_count);
            Context::set('gallery_total_page_count', $gallery_total_page_count);
            Context::set('galley_page_navigation', $galley_page_navigation);
          }


          //템플릿 객체생성 및 스킨파일 컴파일
          $oTemplate = &TemplateHandler::getInstance();
          $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
          return $oTemplate->compile($tpl_path, "lightgallery");

        }
    }
?>
