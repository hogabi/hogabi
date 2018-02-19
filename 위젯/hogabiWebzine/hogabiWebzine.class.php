<?php
    /**
     * @class hogabiWebzine
     * @author hogabi (hogabi@gmail.com)
     **/

    class hogabiWebzine extends WidgetHandler {
        function proc($args) {
            // 정렬 대상
            if(!in_array($args->order_target, array('list_order','update_order','regdate','rand()','voted_count'))) $args->order_target = 'list_order';

            // 정렬 순서
            if(!in_array($args->order_type, array('asc','desc'))) $args->order_type = 'asc';

            // 출력 목록수
            $args->list_count = (int)$args->list_count;
            if(!$args->list_count) $args->list_count = 4;

			// 페이지 수
			$args->page_count = (int)$args->page_count;
			if(!$args->page_count) $args->page_count = 1;

            // 제목 길이 자르기
            if(!$args->subject_cut_size) $args->subject_cut_size = 0;

            // 내용 길이 자르기
            if(!$args->content_cut_size) $args->content_cut_size = 100;

            // 썸네일 생성 방법
            if(!$args->thumbnail_type) $args->thumbnail_type = 'crop';

            // 썸네일 가로 크기
            if(!$args->thumbnail_width) $args->thumbnail_width = 60;

            // 썸네일 세로 크기
            if(!$args->thumbnail_height) $args->thumbnail_height = 60;

			// 새글
			if(!$args->duration_new) $args->duration_new = 12;

            // 보기 옵션
            $args->option_view_arr = explode(',',$args->option_view);

			// 링크 방식 관련
			if(!$args->hyperlink) $args->hyperlink = 'Y';
			if(!$args->hyperlink_src) $args->hyperlink_src = 'article';
			if(!$args->hyperlink_type) $args->hyperlink_type = 'currentwindow';

			// 특정 분류만 출력
			if($args->category_srl) {
				$args->specific_category = true;
				$args->specific_category_srl_list = explode(',', $args->category_srl);
			}
			else $args->specific_category = false;

			// 표시 권한
			$args->view_permission_arr = explode(',',$args->view_permission);
			$vp_view = false; $vp_list = false; $vp_write_document = false; $vp_write_comment = false;
			if (count($args->view_permission_arr)) {
				foreach($args->view_permission_arr as $kk => $val) {
					if ($val == 'list') $vp_list = true;
					else if ($val == 'view') $vp_view = true;
					else if ($val == 'write_document') $vp_write_document = true;
					else if ($val == 'write_comment') $vp_write_comment = true;
				}
			}
			$vp_exist = false;
			if ($vp_view || $vp_list || $vp_write_document || $vp_write_comment) $vp_exist = true;

			// 비밀글 표시 방법
			if (!$args->view_secret_document) $args->view_secret_document = 'all_user';

            // 내부적으로 쓰이는 변수 설정
            $oModuleModel = &getModel('module');
            $module_srls = $args->modules_info = $args->module_srls_info = $args->mid_lists = array();
            $site_module_info = Context::get('site_module_info');

			// 대상 모듈이 선택되어 있지 않으면 해당 사이트의 전체 모듈을 대상으로 함
			if(!$args->module_srls){
				unset($obj);
				$obj->site_srl = (int)$site_module_info->site_srl;
				$output = executeQueryArray('widgets.hogabiWebzine.getMids', $obj);
				if($output->data) {
					foreach($output->data as $key => $val) {
						// 권한 체크
						$gg = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($val->module_srl), $_SESSION['logged_info']);
						if ($vp_exist && !(($vp_view && $gg->view) || ($vp_list && $gg->list) ||
							($vp_write_document && $gg->write_document) ||
							($vp_write_comment && $gg->write_comment))) continue;

						$args->modules_info[$val->mid] = $val;
						$args->module_srls_info[$val->module_srl] = $val;
						$args->mid_lists[$val->module_srl] = $val->mid;
						$module_srls[] = $val->module_srl;
					}
				}

				$args->modules_info = $oModuleModel->getMidList($obj);
			// 대상 모듈이 선택되어 있으면 해당 모듈만 추출
			} else {
				$obj->module_srls = $args->module_srls;
				$output = executeQueryArray('widgets.hogabiWebzine.getMids', $obj);
				if($output->data) {
					foreach($output->data as $key => $val) {
						$args->modules_info[$val->mid] = $val;
						$args->module_srls_info[$val->module_srl] = $val;
						//$module_srls[] = $val->module_srl;
					}
					$idx_bef = explode(',',$args->module_srls);
					
					// 모듈 정보로부터 권한 체크
					$idx_aft = '';
					foreach($idx_bef as $kk => $msrl) {
						$gg = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($msrl), $_SESSION['logged_info']);
						if ($vp_exist && !(($vp_view && $gg->view) || ($vp_list && $gg->list) ||
							($vp_write_document && $gg->write_document) ||
							($vp_write_comment && $gg->write_comment))) continue;

						if ($idx_aft != '') $idx_aft = $idx_aft . ',';
						$idx_aft = $idx_aft . $msrl;
					}
					$idx = explode(',',$idx_aft);

					for($i=0,$c=count($idx);$i<$c;$i++) {
						$srl = $idx[$i];
						if(!$args->module_srls_info[$srl]) continue;
						$args->mid_lists[$srl] = $args->module_srls_info[$srl]->mid;
						$module_srls[] = $srl;
					}
				}
			}

			// 아무런 모듈도 검색되지 않았다면 종료
			if(!count($args->modules_info) || !count($args->mid_lists)) return Context::get('msg_not_founded');
			$args->module_srl = implode(',',$module_srls);

            /**
             * 컨텐츠 추출
             **/
            if($args->document_srls){
                $content_items = $this->_getSpecDocumentItems($args);
            } else {
                $content_items = $this->_getDocumentItems($args);
            }            
            
            $output = $this->_compile($args,$content_items);
            return $output;
        }

        function _getDocumentItems($args){
            // document 모듈의 model 객체를 받아서 결과를 객체화 시킴
            $oDocumentModel = &getModel('document');

            // 분류 구함
            $obj->module_srl = $args->module_srl;
            $output = executeQueryArray('widgets.hogabiWebzine.getCategories',$obj);
            if($output->toBool() && $output->data) {
                foreach($output->data as $key => $val) {
					// 최상위 여부 파악
					if ($args->category_range == 'first' && $val->parent_srl) continue;
					// 특정 분류만 출력 여부 파악
					if ($args->specific_category && !in_array($val->category_srl, $args->specific_category_srl_list)) continue;

                    $category_lists[$val->module_srl][$val->category_srl] = $val;
                }
            }

            // 글 목록을 구함 (+권한 확인)
            $obj->module_srl = $args->module_srl;
            $obj->sort_index = $args->order_target;
            $obj->order_type = $args->order_type=="desc"?"asc":"desc";
			$obj->list_count = 100; // 한번에 가져올 글 목록 개수
			if ($args->category_srl) $obj->category_srl = $args->category_srl;
			$pagecount = 0;
			$needcount = $args->list_count * $args->page_count; // 가져와야 하는 글 개수
			$obj->statusList = array('PUBLIC','SECRET');
			$getcount = 0; // 가져온 글 개수
            $content_items = array();
            $first_thumbnail_idx = -1;
			while(true) {
				$pagecount = $pagecount + 1;
				$obj->page = $pagecount;
				$output = executeQueryArray('widgets.hogabiWebzine.getNewestDocuments', $obj);
				// 더이상 결과가 없으면 break
				if(!$output->toBool() || !$output->data || !count($output->data)) break;
				// 권한 확인해서 문서 목록 생성하기
                foreach($output->data as $key => $attribute) {
                    $oDocument = new documentItem();
                    $oDocument->setAttribute($attribute, false);

					if ($getcount >= $needcount) break;

					// 해당 문서가 비밀글일 경우 권한이 있는지 확인, 없으면 continue
					if ($args->view_secret_document == 'use_permission' && $oDocument->isSecret() && !$oDocument->isGranted()) continue;
					else if ($args->view_secret_document == 'not_show' && $oDocument->isSecret()) continue;

					// 권한이 있다면 객체화 준비
                    $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                    $document_srls[] = $oDocument->document_srl;
					$getcount = $getcount + 1;
					if ($getcount >= $needcount) break;
                }
				if ($getcount >= $needcount) break;
				if ($pagecount >= $output->total_page) break;
			}

            // 결과가 있으면 각 문서 객체화를 시킴
            if($getcount > 0) {
                $oDocumentModel->setToAllDocumentExtraVars();

                for($i=0,$c=count($document_srls);$i<$c;$i++) {
                    $oDocument = $GLOBALS['XE_DOCUMENT_LIST'][$document_srls[$i]];
                    $document_srl = $oDocument->document_srl;
                    $module_srl = $oDocument->get('module_srl');
                    $category_srl = $oDocument->get('category_srl');
                    
                    $thumbnail = $oDocument->getThumbnail($args->thumbnail_width,$args->thumbnail_height,$args->thumbnail_type);

                    $content_item = new hogabiWebzineItem( $args->module_srls_info[$module_srl]->browser_title );
                    $content_item->adds($oDocument->getObjectVars());

					$content_item->setTitle($oDocument->getTitle());
					$content_item->setNickName($oDocument->get('nick_name'));
                    $content_item->setCategory( $category_lists[$module_srl][$category_srl]->title );
                    $content_item->setDomain( $args->module_srls_info[$module_srl]->domain );
                    $content_item->setContent($oDocument->getSummary($args->content_cut_size));

					// 링크 방식 변경
					if ($args->hyperlink == 'N') $content_item->setLink("#");
					else if($args->hyperlink_src == 'article') $content_item->setLink( getSiteUrl($domain,'','document_srl',$document_srl) );
					else if($args->hyperlink_src == 'extravar') $content_item->setLink($oDocument->getExtraEidValue($args->hyperlink_extravar));
					else $content_item->setLink( getSiteUrl($domain,'','document_srl',$document_srl) );

                    $content_item->setThumbnail($thumbnail);
					$content_item->setVotedCount($oDocument->get('voted_count'));
					if ($args->extravar_name) $content_item->setExtraVar($oDocument->getExtraEidValueHTML($args->extravar_name));
					$content_item->setExtraImages($oDocument->printExtraImages($args->duration_new * 60 * 60));
                    $content_item->add('mid', $args->mid_lists[$module_srl]);
                    if($first_thumbnail_idx==-1 && $thumbnail) $first_thumbnail_idx = $i;
                    $content_items[] = $content_item;
                }

                $content_items[0]->setFirstThumbnailIdx($first_thumbnail_idx);
            }
            return $content_items;
        }

        
        function _getSpecDocumentItems($args){
            // document 모듈의 model 객체를 받아서 결과를 객체화 시킴
            $oDocumentModel = &getModel('document');

            // 분류 구함
            $obj->module_srl = $args->module_srl;
            $output = executeQueryArray('widgets.hogabiWebzine.getCategories',$obj);
            if($output->toBool() && $output->data) {
                foreach($output->data as $key => $val) {
					// 최상위 여부 파악
					if ($args->category_range == 'first' && $val->parent_srl) continue;
					// 특정 분류만 출력 여부 파악
					if ($args->specific_category && !in_array($val->category_srl, $args->specific_category_srl_list)) continue;

                    $category_lists[$val->module_srl][$val->category_srl] = $val;
                }
            }            
            
            // 글 목록을 구함
			$document_srl_list = explode(',', $args->document_srls);

            
            // 결과가 있으면 각 문서 객체화를 시킴
            $content_items = array();
            $first_thumbnail_idx = -1;
            if(count($document_srl_list)) {
                foreach($document_srl_list as $key => $val) {
					$obj->document_srl = $val;
					$output = executeQuery('widgets.hogabiWebzine.getNewestSpecDocuments', $obj);
                    $oDocument = new documentItem();
                    $oDocument->setAttribute($output->data, false);
                    $GLOBALS['XE_DOCUMENT_LIST'][$oDocument->document_srl] = $oDocument;
                    $document_srls[] = $oDocument->document_srl;
                }
                $oDocumentModel->setToAllDocumentExtraVars();

                for($i=0,$c=count($document_srls);$i<$c;$i++) {
                    
                    $oDocument = $GLOBALS['XE_DOCUMENT_LIST'][$document_srls[$i]];
                    $document_srl = $oDocument->document_srl;
                    $module_srl = $oDocument->get('module_srl');
                    $category_srl = $oDocument->get('category_srl');
                    $thumbnail = $oDocument->getThumbnail($args->thumbnail_width,$args->thumbnail_height,$args->thumbnail_type);

                    $content_item = new cameronSliderItem( $args->module_srls_info[$module_srl]->browser_title );
                    $content_item->adds($oDocument->getObjectVars());
                    
					$content_item->setTitle($oDocument->getTitle());
					$content_item->setNickName($oDocument->get('nick_name'));
                    $content_item->setCategory( $category_lists[$module_srl][$category_srl]->title );
                    $content_item->setDomain( $args->module_srls_info[$module_srl]->domain );
                    $content_item->setContent($oDocument->getSummary($args->content_cut_size));
					
                    // 링크 방식 변경
					if ($args->hyperlink == 'N') $content_item->setLink("#");
					else if($args->hyperlink_src == 'article') $content_item->setLink( getSiteUrl($domain,'','document_srl',$document_srl) );
					else if($args->hyperlink_src == 'extravar') $content_item->setLink($oDocument->getExtraEidValue($args->hyperlink_extravar));
					else $content_item->setLink( getSiteUrl($domain,'','document_srl',$document_srl) );
                    
                    $content_item->setThumbnail($thumbnail);
                    $content_item->setVotedCount($oDocument->get('voted_count'));
					if ($args->extravar_name) $content_item->setExtraVar($oDocument->getExtraEidValueHTML($args->extravar_name));
					$content_item->setExtraImages($oDocument->printExtraImages($args->duration_new * 60 * 60));

                    
                    $content_item->add('mid', $args->mid_lists[$module_srl]);
                    $content_items[] = $content_item;
                }
                
                $content_items[0]->setFirstThumbnailIdx($first_thumbnail_idx);
            }
            return $content_items;
        }                
        

        function _getSummary($content, $str_size = 50)
        {
            $content = preg_replace('!(<br[\s]*/{0,1}>[\s]*)+!is', ' ', $content);

            // </p>, </div>, </li> 등의 태그를 공백 문자로 치환
            $content = str_replace(array('</p>', '</div>', '</li>'), ' ', $content);

            // 태그 제거
            $content = preg_replace('!<([^>]*?)>!is','', $content);

            // < , > , " 를 치환
            $content = str_replace(array('&lt;','&gt;','&quot;','&nbsp;'), array('<','>','"',' '), $content);

            // 연속된 공백문자 삭제
            $content = preg_replace('/ ( +)/is', ' ', $content);

            // 문자열을 자름
            $content = trim(cut_str($content, $str_size, $tail));

            // >, <, "를 다시 복구
            $content = str_replace(array('<','>','"'),array('&lt;','&gt;','&quot;'), $content);

            // 영문이 연결될 경우 개행이 안 되는 문제를 해결
            $content = preg_replace('/([a-z0-9\+:\/\.\~,\|\!\@\#\$\%\^\&\*\(\)\_]){20}/is',"$0-",$content);
            return $content; 
        }


        function _compile($args,$content_items){
            $oTemplate = &TemplateHandler::getInstance();

            // 위젯에 넘기기 위한 변수 설정
			$widget_info->cs_color = $args->cs_color;
            $widget_info->modules_info = $args->modules_info;
            $widget_info->option_view_arr = $args->option_view_arr;
            $widget_info->list_count = $args->list_count;
			$widget_info->page_count = $args->page_count;
            $widget_info->subject_cut_size = $args->subject_cut_size;
            $widget_info->content_cut_size = $args->content_cut_size;

            $widget_info->thumbnail_type = $args->thumbnail_type;
            $widget_info->thumbnail_width = $args->thumbnail_width;
            $widget_info->thumbnail_height = $args->thumbnail_height;
            $widget_info->mid_lists = $args->mid_lists;

            $widget_info->show_category = $args->show_category;

            $widget_info->content_items = $content_items;
            unset($args->option_view_arr);
            unset($args->modules_info);

			$widget_info->show_icon = $args->show_icon;
			$widget_info->duration_new = $args->duration_new * 60*60;

            Context::set('colorset', $args->colorset);
            Context::set('widget_info', $widget_info);

            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            return $oTemplate->compile($tpl_path, "content");
        }
    }

    class hogabiWebzineItem extends Object {

        var $browser_title = null;
        var $document_title = null;
        var $document_url = null;
        var $has_first_thumbnail_idx = false;
        var $first_thumbnail_idx = null;
        var $contents_link = null;
        var $domain = null;
		var $extra_var = null;
		var $extra_var2 = null;
		var $extra_var3 = null;
		var $extra_var4 = null;
		var $extra_var5 = null;

        function hogabiWebzineItem($browser_title=''){
            $this->browser_title = $browser_title;
        }
        function setContentsLink($link){
            $this->contents_link = $link;
        }
        function setFirstThumbnailIdx($first_thumbnail_idx){
            if(is_null($this->first_thumbnail) && $first_thumbnail_idx>-1) {
                $this->has_first_thumbnail_idx = true;
                $this->first_thumbnail_idx= $first_thumbnail_idx;
            }
        }
        function setDomain($domain) {
            static $default_domain = null;
            if(!$domain) {
                if(is_null($default_domain)) $default_domain = Context::getDefaultUrl();
                $domain = $default_domain;
            }
            $this->domain = $domain;
        }
        function setLink($url){
            $this->add('url',$url);
        }
        function setTitle($title){
            $this->add('title',$title);
        }

        function setThumbnail($thumbnail){
            $this->add('thumbnail',$thumbnail);
        }
        function setContent($content){
            $this->add('content',$content);
        }
        function setRegdate($regdate){
            $this->add('regdate',$regdate);
        }
        function setNickName($nick_name){
            $this->add('nick_name',$nick_name);
        }
		function setVotedCount($voted_count){
			$this->add('voted_count',$voted_count);
		}
		function setExtraImages($extra_images)
		{
			$this->add('extra_images',$extra_images);
		}
		function setExtraVar($extravar){
			$this->extra_var = $extravar;
		}

        // 글 작성자의 홈페이지 주소를 저장
        function setAuthorSite($site_url){
            $this->add('author_site',$site_url);
        }
        function setCategory($category){
            $this->add('category',$category);
        }
		function setCategorySrl($category_srl){
			$this->add('category_srl',$category_srl);
		}

		// 댓글의 게시물 관련 정보
		function setDocumentTitle($document_title){
			$this->document_title = $document_title;
		}
		function setDocumentURL($document_url){
			$this->document_url = $document_url;
		}
		function getDocumentTitle(){
			return $this->document_title;
		}
		function getDocumentURL(){
			return $this->document_url;
		}

        function getBrowserTitle(){
            return $this->browser_title;
        }
        function getDomain() {
            return $this->domain;
        }
        function getContentsLink(){
            return $this->contents_link;
        }

        function getFirstThumbnailIdx(){
            return $this->first_thumbnail_idx;
        }

        function getLink(){
            return $this->get('url');
        }
        function getModuleSrl(){
            return $this->get('module_srl');
        }
		function getMid(){
            return $this->get('mid');
		}
        function getTitle($cut_size = 0, $tail='...'){
            $title = strip_tags($this->get('title'));

            if($cut_size) $title = cut_str($title, $cut_size, $tail);

            $attrs = array();
            if($this->get('title_bold') == 'Y') $attrs[] = 'font-weight:bold';
            if($this->get('title_color') && $this->get('title_color') != 'N') $attrs[] = 'color:#'.$this->get('title_color');

            if(count($attrs)) $title = sprintf("<span style=\"%s\">%s</span>", implode(';', $attrs), htmlspecialchars($title));

            return $title;
        }
        function getContent(){
            return $this->get('content');
        }
        function getCategory(){
            return $this->get('category');
        }
		function getCategorySrl(){
			return $this->get('category_srl');
		}
        function getNickName(){
            return $this->get('nick_name');
        }
        function getAuthorSite(){
            return $this->get('author_site');
        }
        function getCommentCount(){
            $comment_count = $this->get('comment_count');
            return $comment_count>0 ? $comment_count : '';
        }
        function getRegdate($format = 'Y.m.d H:i:s') {
            return zdate($this->get('regdate'), $format);
        }
        function haveFirstThumbnail() {
            return $this->has_first_thumbnail_idx;
        }
        function getThumbnail(){
            return $this->get('thumbnail');
        }
        function getMemberSrl() {
            return $this->get('member_srl');
        }
		function getVotedCount() {
			return $this->get('voted_count');
		}
		function printExtraImages()
		{
			return $this->get('extra_images');
		}
		function getExtraVar() {
			return $this->extra_var;
		}
    }
?>
