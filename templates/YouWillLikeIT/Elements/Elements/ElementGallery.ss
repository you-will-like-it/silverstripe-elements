<%-- Basic markup only: thumbnails link to the large image. Projects add a lightbox in their theme. --%>
<div class="ywli-gallery">
    <% if $Heading || $Intro %>
        <header class="ywli-gallery__header">
            $Heading('ywli-gallery__title')
            <% if $Intro %><p class="ywli-gallery__intro">$Intro</p><% end_if %>
        </header>
    <% end_if %>
    <% if $SortedImages.exists %>
        <ul class="ywli-gallery__items">
            <% loop $SortedImages %>
                <li class="ywli-gallery__item">
                    <a class="ywli-gallery__link" href="$ScaleMaxWidth(2000).URL">
                        <% with $Fill(800,600) %>
                            <img class="ywli-gallery__image" src="$URL" width="$Width" height="$Height" alt="$Up.Title.ATT" loading="lazy" decoding="async">
                        <% end_with %>
                    </a>
                </li>
            <% end_loop %>
        </ul>
    <% end_if %>
</div>
