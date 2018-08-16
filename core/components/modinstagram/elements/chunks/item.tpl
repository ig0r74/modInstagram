<li>
    <a target="_blank" href="{$link}" title="{$caption_text | strip : true | replace : '⠀' : '' | truncate : 100}">
        {switch $type}
            {case 'image', 'carousel'}
                <img class="img-responsive" src="{$standard_resolution}" alt="{$caption_text | strip : true | truncate : 100}">
            {case 'video'}
                <video><source src="{$standard_resolution}" type="video/mp4"></video>
        {/switch}
    </a>
</li>