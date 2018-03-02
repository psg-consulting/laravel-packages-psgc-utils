<?php
namespace PsgcLaravelPackages\Utils;

class ViewHelpers
{
    // $classArrays is an array of arrays
    public static function renderClassStr($classArrays) {
        $classStr = '';
        foreach ($classArrays as $i => $ca) {
            $classStr .= ' '.implode(' ',$ca);
        }
        trim($classStr);
        return $classStr;
    } // renderClassStr()

    public static function makeActiveLink($linkRoute) {
        $route =  \Request::route()->getName();
        //$is = ($route ==  $linkRoute )  ? 1 : 0;
        if ( $route ==  $linkRoute )  {
            return 'active';
        } else {
            return '';
        }
    }

    public static function renderList($list,$field=null)
    {
        if ( !empty($field) ) {
            $list2 = [];
            foreach ($list as $l) {
                $list2[] = $l->{$field};
            }
            $str = implode(',',$list2);
        } else {
            $str = implode(',',$list);
        }
        return $str;
    }


    public static function makeNiceDate($dateIn,$numberFormat=false, $includeTime=false)
    {
        if ( empty($dateIn) ) {
            return 'N/A';
        }
        $format = $numberFormat ? 'm/d/Y' : 'F d, Y';
        if ($includeTime) {
            $format .= ' G:i';
        }
        //$dateOut = date('F d, Y G:i', strtotime($dateIn));
        $dateOut = date($format, strtotime($dateIn));
        return $dateOut;
    }

    // $handleNull = 1 : treat null as an 'undefined' state
    public static function makeNiceBinary($is,$abbreviate=0,$handleNull=0)
    {

        $trueStr = $abbreviate ? 'Y' : 'Yes';
        $falseStr = $abbreviate ? 'N' : 'No';

        if ( is_null($is) ) {
            $strOut = $handleNull ? 'N/A' : $falseStr;
        } else {
            $strOut = empty($is) ? $falseStr : $trueStr;
        }

        return $strOut;
    }

    // %TODO DEPRECATED: migrate to CurrencyHelpers  
    public static function makeNiceCurrency($str)
    {
        $str = '$'.number_format($str, 2, '.',',');
        return $str;
    }

    public static function makeNicePercentage($str,$option=null)
    {
        switch ($option) {
            case 'rounded-no-decimal':
                $str = number_format((float)$str, 0, '.', '').'%';
                break;
            default:
                $str = number_format((float)$str, 3, '.', '').'%';
        }
        
        //$str = sprintf("%.4f%%", intval($str));
        //$formatter = new \NumberFormatter('en_US', \NumberFormatter::PERCENT);
        //$str = $formatter->format(intval($str));
        return $str;
    }

    public static function makeBoxedTimestamp($ts)
    {
        $month = date('M', strtotime($ts));
        $datenum = date('d', strtotime($ts));
        $year = date('Y', strtotime($ts));
        $time = date('g', strtotime($ts)).':'.date('i',strtotime($ts));
        $period = date('a', strtotime($ts));

        $html = '<section class="box-boxed_timestamp">'."\n";
        $html .= '<article class="floatLeft">'."\n";
        $html .= '<div class="tag-month">'.$month.'</div>'."\n";
        $html .= '<div class="tag-datenum">'.$datenum.'</div>'."\n";
        $html .= '<div class="tag-year">'.$year.'</div>'."\n";
        $html .= '</article>'."\n";
        $html .= '<article class="floatLeft">'."\n";
        $html .= '<div class="tag-time">'.$time.' '.$period.'</div>'."\n";
        $html .= '</article>'."\n";
        $html .= '<article class="clearBoth"></article>'."\n";
        $html .= '</section>'."\n";

        return $html;
    }

    public static function linkToMediafile($mediafile, $params = [], $attrs = [])
    {
        switch ($mediafile->mimetype) {
            default:

                $url = $mediafile->getMediaUrlCDN();
        }

        $html = html_entity_decode(
                                    link_to(
                                        $url,
                                        $mediafile->guid,
                                        $attrs
                                    )
                                  );

        return $html;
    }

    public static function linkToMediafileName($mediafile, $params = [], $attrs = [])
    {
        switch ($mediafile->mimetype) {
            default:
                $url = $mediafile->getMediaUrlCDN();
        }

        $html = html_entity_decode(
            link_to(
                $url,
                $mediafile->ogfilename,
                $attrs
            )
        );

        return $html;
    }

    public static function linkToRouteWithHtml($route, $html, $params = [], $attrs = [])
    {
        $html = html_entity_decode(
                                    link_to_route(
                                        $route,
                                        $html,
                                        $params,
                                        $attrs
                                    )
                                  );

        return $html;
    }

    public static function linkToWithHtml($url, $html, $attrs = [])
    {
        $html = html_entity_decode(
                                    link_to(
                                        $url,
                                        $html,
                                        $attrs
                                    )
                                  );

        return $html;
    }

    public static function linkToRouteWithImg($route, $imgPath, $imgAlt, $imgAttrs = [], $linkClasses = [])
    {
        $html = html_entity_decode(
                                    link_to_route(
                                        $route,
                                        \Html::image(
                                            $imgPath,
                                            $imgAlt,
                                            $imgAttrs //array('class'=>'tag-usericon'), array( 'width' => 70, 'height' => 70 )
                                        )
                                    )
                                  );

        return $html;
    }

    public static function linkToWithImg($url, $imgPath, $imgAlt, $imgClasses = [], $linkClasses = [])
    {
        $html = html_entity_decode(
                                    link_to(
                                        $url,
                                        \HTML::image(
                                            $imgPath,
                                            $imgAlt,
                                            $imgClasses //array('class'=>'tag-usericon')
                                        ),
                                        $linkClasses
                                    )
                                  );

        return $html;
    }

    /*
     * ViewHeleprs for Bootstrap Btn Dropdowns
     *   http://getbootstrap.com/components/#btn-dropdowns
     */
    // This version is supplied a list of specific routes
    public static function renderBtnDropdown($urls,$title='')
    {
        $html = '';
        $html .= '<div class="btn-group">';
        $html .=    '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .=        $title.' <span class="caret"></span>';
        $html .=    '</button>';
        $html .=    '<ul class="dropdown-menu">';
        foreach ($urls as $url => $v) {
            $html .= '<li>'.link_to($url,$v).'</li>';
        }
        $html .=    '</ul>';
        $html .= '</div>';
        return $html;
    }

    // This version is supplied one route with a list of query params (options)
    public static function renderBtnDropdownByQuery($baseurl, $options=[], $title='')
    {
        $html = '';
        $html .= '<div class="btn-group">';
        $html .=    '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .=        $title.' <span class="caret"></span>';
        $html .=    '</button>';
        $html .=    '<ul class="dropdown-menu">';
        foreach ($options as $k => $v) {
            $url = $baseurl.'/?key='.$k;
            $html .=        '<li>'.link_to($url,$v).'</li>';
        }
        $html .=    '</ul>';
        $html .= '</div>';
        return $html;
    }

    // random lorem ipsum filler
    public static function getRandomCopy()
    {
        $strArray = [
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse venenatis ut velit iaculis faucibus. Aliquam erat volutpat. Aenean tincidunt ut dolor eu gravida. Praesent feugiat ornare enim, dapibus cursus urna maximus eget. Cras a laoreet velit.",
            "Aliquam et neque metus. Cras tempus vel ante non scelerisque. Nunc pellentesque tincidunt augue, ut porta augue tincidunt vel. Fusce finibus lorem elit, non pulvinar urna dapibus quis. Vestibulum cursus sollicitudin sagittis. Sed eget leo dolor. Duis erat mauris, tristique interdum tincidunt ut, faucibus non tellus. Cras ut rhoncus ex, eu auctor dui.",
            "Etiam congue dolor sed quam pharetra, maximus iaculis lacus accumsan. Maecenas eleifend libero eget nunc eleifend, vitae sodales arcu condimentum. Integer quis magna rhoncus, iaculis ligula faucibus, tempor eros.",
            "Vivamus ut malesuada lacus. Nunc semper eu risus vel cursus. Vivamus id faucibus enim. Vivamus commodo tellus justo, vel sollicitudin sapien posuere et.",
            "Quisque lectus nisi, mollis convallis placerat sit amet, elementum sed eros. Duis non commodo libero, in malesuada lorem. Nam ultrices risus quis metus blandit condimentum. Nunc aliquet ultricies posuere. Aliquam erat volutpat. Aenean mi nunc, euismod a nisi id, suscipit fermentum turpis. Aliquam sit amet venenatis urna, vel congue massa.",
            "Etiam commodo ante ac elit hendrerit, eu tempus lacus porta. Morbi posuere auctor interdum. Cras nec venenatis lacus, id malesuada mi. Proin ut ex convallis lorem blandit pellentesque vel vitae quam. Nam a commodo justo, nec suscipit mauris. Pellentesque nibh mauris, scelerisque et tincidunt nec, porttitor et augue. Nam ac justo nunc. Pellentesque fringilla laoreet augue. Nullam tincidunt facilisis ligula, at tristique nibh sodales a.",
            "Nulla tortor elit, lobortis et sapien in, rutrum aliquet lectus. Donec hendrerit elit pretium ipsum accumsan, et gravida ex cursus.",
            "In nisl dui, viverra ac pellentesque vel, convallis facilisis velit. Maecenas ut libero nisi. Pellentesque lacinia, dui a suscipit suscipit, sem risus sagittis eros, eu ullamcorper leo mi vitae odio. Maecenas accumsan augue ut quam gravida, nec malesuada ligula dignissim. In hac habitasse platea dictumst. Nulla in nibh molestie, tristique elit at, rhoncus tellus. Etiam commodo justo et sem vehicula feugiat. Nulla vestibulum arcu risus, eget fringilla purus tincidunt a. Duis at nibh at libero aliquet condimentum sed laoreet dolor.",
            "In quis ex suscipit, congue nulla a, consectetur ipsum. Etiam hendrerit ligula a quam efficitur fringilla.",
            "Nunc placerat condimentum nunc, vel ullamcorper elit consectetur ut. Proin varius vitae est vitae finibus. Nunc quis pulvinar lectus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis vitae turpis vel est porta sodales eu ac urna.",
            "Nunc non mauris vel justo volutpat lacinia nec in ipsum. Sed condimentum odio tortor, vitae gravida arcu dapibus tempus. Donec varius neque a tellus elementum, ac imperdiet enim interdum. Nunc dolor purus, tincidunt eget metus sit amet, semper commodo odio. Morbi in purus at velit pellentesque semper vel ac mauris. Nullam laoreet augue non arcu volutpat hendrerit. Praesent eget sodales lacus.",
        ];

        $index = mt_rand( 0, (count($strArray)-1) ); 
        return $strArray[$index];
    }

}
