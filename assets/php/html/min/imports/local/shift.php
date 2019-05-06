<header class=shift-header id=shift_header><div class=content-wrapper><div class="section counters"><div class="inactive badge total"aria-label="No SHiFT Codes Available"title="No SHiFT Codes Available"id=shift_header_count_total><strong class=count>0</strong><span class="fas fa-key"></span></div><button aria-label="No New SHiFT Codes"title="No New SHiFT Codes"class="inactive badge new"aria-disabled=true aria-pressed=false data-pressed=false disabled id=shift_header_count_new><strong class=count>0</strong><span class="fas fa-star"></span></button><button aria-label="No Expiring SHiFT Codes"title="No Expiring SHiFT Codes"class="inactive badge exp"aria-disabled=true aria-pressed=false data-pressed=false disabled id=shift_header_count_exp><strong class=count>0</strong><span class="fas fa-exclamation-triangle"></span></button></div><div class="section sort"><button aria-label="Change Sort"title="Change Sort"aria-disabled=true aria-pressed=false data-pressed=false disabled id=shift_header_sort aria-haspopup=true autocomplete=off><span class="fas fa-sort-amount-down"></span></button><div class=dropdown hidden aria-expanded=false data-expanded=false data-hidden=true id=shift_header_sort_dropdown><span class=arrow></span><ul class=panel role=menu><span class=description>Sort codes by:</span><li role=menuitem><button aria-disabled=true aria-pressed=true data-pressed=true disabled data-value=default><span>Default</span></button><li role=menuitem><button aria-disabled=true aria-pressed=false data-pressed=false disabled data-value=newest><span>Newest</span></button><li role=menuitem><button aria-disabled=true aria-pressed=false data-pressed=false disabled data-value=oldest><span>Oldest</span></button></ul></div></div></div></header><main class="content-wrapper feed"data-filter=none data-sort=default id=panel_feed><div class=overlay id=shift_overlay><?php include("./assets/php/html/min/imports/local/spinner.php"); ?><div class=error hidden aria-hidden=true><strong><div>No SHiFT Codes are currently available</div><span class="fas fa-heart-broken"></span><div>Please try again later</div></strong></div></div></main><div hidden aria-hidden=true id=panel_feed_template></div><template id=panel_template><div class=panel data-extraInfo=false aria-expanded=false data-expanded=false><div class="flag new"aria-label="New SHiFT Code"title="New SHiFT Code"><span class="fas fa-star"></span></div><div class="exp flag"aria-label="Expiring SHiFT Code"title="Expiring SHiFT Code"><span class="fas fa-exclamation-triangle"></span></div><div class=hashTargetOverlay></div><div class=header><div class=top><span class="fas fa-key"aria-label="SHiFT Code"title="SHiFT Code"></span><div class=title><strong class=reward>5 Golden Keys</strong><div class=description>SHiFT Code</div></div><button aria-label="Expand SHiFT Code"title="Expand SHiFT Code"class="bubble-parent toggle"><span class="bubble bubble-light"></span><span class="fas fa-chevron-circle-down"></span></button></div><div class=bottom><div class=progress-bar aria-valuemax=100 aria-valuemin=0 aria-valuenow=0 role=progressbar><span class=progress></span></div></div></div><div class=body><span class="fas fa-key background"></span><div class="section rel"><strong class=title>Release Date:</strong><div class=content></div></div><div class="section exp"><strong class=title>Expiration Date:</strong><div class=content></div></div><div class="section src"><strong class=title>Source:</strong><div class=content><a rel="external noopener"target=_blank><span class="fas fa-external-link-square-alt"aria-label="External Link"title="External Link"></span> <span class=text></span></a></div></div><div class="section inactive notes"><strong class=title>Notes:</strong><ul class="content styled"></ul></div><div class=separator></div><div class="section pc"><strong class=title></strong><div class=content><span class=display></span><input aria-hidden=true class=value hidden tabindex=-1><button aria-label="Copy to Clipboard"title="Copy to Clipboard"class=copy><span class="fas fa-clipboard"></span></button></div></div><div class="section xbox"><strong class=title></strong><div class=content><span class=display></span><input aria-hidden=true class=value hidden tabindex=-1><button aria-label="Copy to Clipboard"title="Copy to Clipboard"class=copy><span class="fas fa-clipboard"></span></button></div></div><div class="section ps"><strong class=title></strong><div class=content><span class=display></span><input aria-hidden=true class=value hidden tabindex=-1><button aria-label="Copy to Clipboard"title="Copy to Clipboard"class=copy><span class="fas fa-clipboard"></span></button></div></div></div></div></template><template id=panel_filter_overlay_template><div class=filter-overlay hidden aria-hidden=true data-visible=hover-hide><div class=content-container><div class=title><span class="fas fa-filter"></span><span>Filter Active</span></div><button aria-label="Remove active filter"title="Remove active filter"class=clear>Click to Remove</button></div></div></template>