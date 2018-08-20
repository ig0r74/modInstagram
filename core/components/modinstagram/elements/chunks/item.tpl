{if ($idx - 1) % 3 == 0}
    <div class="row">
{/if}
    <div class="col-sm-6 col-md-4">
        <div class="thumbnail">
            {switch $type}
                {case 'image', 'carousel'}
                    <img src="{$image_standard_resolution}" alt="">
                {case 'video'}
                    <video width="100%" controls="controls">
                        <source src="{$video_standard_resolution}" type="video/mp4">
                    </video>
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