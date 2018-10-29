{if ($idx - 1) % 3 == 0}
    <div class="row">
{/if}
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
            {switch $type}
                {case 'carousel'}
                    <div id="carousel-instagram" class="carousel slide" data-ride="carousel">
                        <ol class="carousel-indicators">
                            {foreach $carousel as $item}
                                <li data-target="#carousel-instagram" data-slide-to="{$item@index}" {$item@index == 0 ? 'class="active"' : ''}></li>
                            {/foreach}
                        </ol>

                        <div class="carousel-inner" role="listbox">
                            {foreach $carousel as $item}
                                <div class="item {$item@index == 0 ? 'active' : ''}">
                                    <img src="{$item.images.standard_resolution.url}" alt="">
                                </div>
                            {/foreach}
                        </div>

                        <a class="left carousel-control" href="#carousel-instagram" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Предыдущий</span>
                        </a>
                        <a class="right carousel-control" href="#carousel-instagram" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Следующий</span>
                        </a>
                    </div>
                {case 'video'}
                    <video width="100%" controls="controls">
                        <source src="{$video_standard_resolution}" type="video/mp4">
                    </video>
                {case default}
                    <img src="{$image_standard_resolution}" alt="">
            {/switch}
            <div class="caption">
                <h3>{$location_name}</h3>
                <p>{$caption_text | strip : true | truncate : 100}</p>
                <p><a href="{$link}" class="btn btn-primary" role="button" rel="nofollow" target="_blank">Подробнее</a></p>
            </div>
        </div>
    </div>
{if $idx % 3 == 0}
    </div>
{/if}