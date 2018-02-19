<?php
    /**
     * @class boderSangjusori
     * @author hogabi
     * @brief 특정 게시판3개를 지정하여 탭으로 분리해서 출력
     * @version 0.1
     **/

    class boardSangjusori extends WidgetHandler {

        /**
         * @brief 위젯의 실행 부분
         *
         * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
         * 결과를 만든후 print가 아니라 return 해주어야 한다
         **/
        function proc($args) {
            $oDocumentModel = &getModel('document');

			$args->subject_cut_size = (int)$args->subject_cut_size;
			if(!$args->subject_cut_size) $args->list_count = 50;


            // 글자 제목 길이
            $widget_info->subject_cut_size = (int)$args->subject_cut_size;
            
            
            
            // 출력 대상 모듈번호
            if (!$args->module_srls_1) $args->module_srls_1 = 116;
            if (!$args->module_srls_2) $args->module_srls_2 = 106;
            if (!$args->module_srls_3) $args->module_srls_3 = 4801;
            
            $widget_info->module_srls_1 = (int)$args->module_srls_1;
            $widget_info->module_srls_2 = (int)$args->module_srls_2;
            $widget_info->module_srls_3 = (int)$args->module_srls_3;            
            
            // 탭 제목
            if (!$args->tab_title_1) $args->tab_title_1 = "알립니다";
            if (!$args->tab_title_2) $args->tab_title_2 = "여론광장";
            if (!$args->tab_title_3) $args->tab_title_3 = "보도자료";
            
            $widget_info->tab_title_1 = $args->tab_title_1;
            $widget_info->tab_title_2 = $args->tab_title_2;
            $widget_info->tab_title_3 = $args->tab_title_3;                        
            
			$args->list_count = (int)$args->list_count;
			if(!$args->list_count) $args->list_count = 5;

            
            // 인수 정리
            $db_args->module_srl = $args->module_srls[0];

            $db_args->module_srls = $args->module_srls;
            $db_args->sort_index = 'documents.list_order';
            $db_args->order_type = 'asc';
            $db_args->list_count = $args->list_count;

            // 지역소식    
            $oDB = &DB::getInstance();
            $query  = "SELECT * from xe_documents where module_srl = " .$widget_info->module_srls_1. " order by regdate DESC limit ".$args->list_count;

            $result = $oDB->_query($query);
            $output = $oDB->_fetch($result);

            if (count($output) == 1) {
                $output = array($output);
            }
            
            
            if(count($output))
            {

                foreach($output as $key => $attribute)
                {
                  $oDocument = new documentItem();
                  $oDocument->setAttribute($attribute, false);
                  $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                  $document_srls[] = $oDocument->document_srl;
                  $output[$key] = $oDocument;
                }
                $oDocumentModel->setToAllDocumentExtraVars();
            }

            $widget_info->newest_documents = $output;

            //여론광장
			$oDB = &DB::getInstance();
			$query  = "SELECT * from xe_documents where module_srl = " .$widget_info->module_srls_2. " order by regdate DESC limit ".$args->list_count;
            
            $result = $oDB->_query($query);
			$output = $oDB->_fetch($result);

            if (count($output) == 1) {
                $output = array($output);
            }


            if(count($output)){

                foreach($output as $key => $attribute)
                {
                    $oDocument = new documentItem();
                    $oDocument->setAttribute($attribute, false);
                    $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                    $document_srls[] = $oDocument->document_srl;
                    $output[$key] = $oDocument;
                }
                $oDocumentModel->setToAllDocumentExtraVars();
            }

            $widget_info->newest_comments = $output;


			$oDB = &DB::getInstance();
            // 보도자료
			$query  = "SELECT * from xe_documents where module_srl = " .$widget_info->module_srls_3. " order by regdate DESC limit ".$args->list_count;

			$result = $oDB->_query($query);
//            if(!is_array($result)) $result = array($result);
            $output = $oDB->_fetch($result);

            if (count($output) == 1) {
                $output = array($output);
            }

            
            if(count($output))
            {
              foreach($output as $key => $attribute)
              {
                $oDocument = new documentItem();
                $oDocument->setAttribute($attribute, false);
                $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                $document_srls[] = $oDocument->document_srl;
                $output[$key] = $oDocument;
              }                  

              $oDocumentModel->setToAllDocumentExtraVars();
            }

            $widget_info->popular_documents = $output;

            Context::set('widget_info', $widget_info);

            // 언어파일 로드
            Context::loadLang($this->widget_path.'lang');

            // 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            Context::set('colorset', $args->colorset);

            // 템플릿 파일을 지정
            $tpl_file = 'list';

            // 템플릿 컴파일
            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
        }
    }
?>
