<?php
    /**
     * @class gallery_frame
     * @author zirho6 (zirho6@nate.com)
     * @brief 겔러리 프레임으로 최신 이미지를 보여주는 모듈
     * @version 0.1
     **/

    class gallery_frame extends WidgetHandler {

        /**
         * @brief 위젯의 실행 부분
         *
         * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
         * 결과를 만든후 print가 아니라 return 해주어야 한다
         **/
       
        function proc($args) {
			
            $gf_widget_count = Context::get('gf_widget_count');
			$gf_widget_count = $gf_widget_count + 1;

			// default vals
			$args->thumbnail_type = isset($args->thumbnail_type) ? $args->thumbnail_type : 'crop';
			$args->shuffling = isset($args->shuffling) ? $args->shuffling : "false";
			$args->pause_on_hover = isset($args->pause_on_hover) ? $args->pause_on_hover : "true";

			$args->panel_width = isset($args->panel_width) ? (int)$args->panel_width : 500;
			$args->panel_height = isset($args->panel_height) ? (int)$args->panel_height : 200;

			$args->frame_width = isset($args->frame_width) ? (int)$args->frame_width : 50;
			$args->frame_height = isset($args->frame_height) ? (int)$args->frame_height : 50;

			$args->total_images_num = isset($args->total_images_num) ? (int)$args->total_images_num : 15;

			$args->sliding_effect = isset($args->sliding_effect) ? $args->sliding_effect : 'easeOutQuad';
			$args->transition_interval = isset($args->transition_interval) ? (int)$args->transition_interval : 3000;
			$args->transition_speed = isset($args->transition_speed) ? (int)$args->transition_speed : 300;

			$args->title_visibility = isset($args->title_visibility) ? $args->title_visibility : "true";
			$args->title_length = isset($args->title_length) ? (int)$args->title_length : 14;
			$args->content_length = isset($args->content_length) ? (int)$args->content_length : 100;
			$args->overlay_position = isset($args->overlay_position) ? $args->overlay_position : 'bottom';

			$args->background_color = isset($args->background_color) ? $args->background_color : 'transparent';
			$args->overlay_color = isset($args->overlay_color) ? $args->overlay_color : 'transparent';
			$args->overlay_opacity = isset($args->overlay_opacity) ? $args->overlay_opacity : '0.4';

			$args->title_size = isset($args->title_size) ? $args->title_size : "20px";
			$args->content_size = isset($args->content_size) ? $args->content_size : "12px";
			$args->writer_size = isset($args->writer_size) ? $args->writer_size : "10px";

			$args->title_color = isset($args->title_color) ? $args->title_color : "transparent";
			$args->content_color = isset($args->content_color) ? $args->content_color : "transparent";
			$args->writer_color = isset($args->writer_color) ? $args->writer_color : "transparent";

			$args->title_font = isset($args->title_font) ? $args->title_font : "'나눔고딕 web','NanumGothic','NanumGothicOTF','나눔고딕','Malgun Gothic','Trebuchet MS','Lucida Grande','Tahoma','Helvetica','Arial',sans-serif";
			$args->content_font = isset($args->content_font) ? $args->content_font : "'나눔고딕 web','NanumGothic','NanumGothicOTF','나눔고딕','Malgun Gothic','Trebuchet MS','Lucida Grande','Tahoma','Helvetica','Arial',sans-serif";
			$args->writer_font = isset($args->writer_font) ? $args->writer_font : "'나눔고딕 web','NanumGothic','NanumGothicOTF','나눔고딕','Malgun Gothic','Trebuchet MS','Lucida Grande','Tahoma','Helvetica','Arial',sans-serif";

			$args->nav_theme = isset($args->nav_theme) ? $args->nav_theme : "light";
			$args->filmstrip_position = isset($args->filmstrip_position) ? $args->filmstrip_position : "bottom";
			$args->clickable = isset($args->clickable) ? $args->clickable : "false";
			
            // query args
			$query_args->module_srls = $args->module_srls;
			$query_args->direct_download = 'Y';
            $query_args->isvalid = 'Y';
			$query_args->list_count = $args->total_images_num;
						
            // get file list
            $files_output = executeQueryArray("widgets.gallery_frame.getImages", $query_args);
			unset($query_args);

			$document_srl_list = array();
            $document_list = array();
			
			if($files_output->data) foreach($files_output->data as $val){ $document_srl_list[] = $val->document_srl; }
			
			unset($files_output);
				
    		if($document_srl_list) {
		        $oDocumentModel = &getModel('document');
				$tmp_document_list = $oDocumentModel->getDocuments($document_srl_list);
				foreach($tmp_document_list as $val) $document_list[] = $val;
				unset($oDocumentModel);
				unset($tmp_document_list);

				//shuffle
				if($args->shuffling == "true") shuffle($document_list);
				$args->document_list = $document_list;
			}

			$args->theme_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);

            Context::set('gf_widget_count', $gf_widget_count);
            Context::set('widget_vals', $args);

            // skin
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            $tpl_file = 'list';
            $oTemplate = &TemplateHandler::getInstance();
            $output = $oTemplate->compile($tpl_path, $tpl_file);

            return $output;
        }
    }
?>