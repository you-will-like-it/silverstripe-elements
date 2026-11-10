<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-text-media<% if $Image.exists %> ywli-text-media--has-image<% end_if %>">
    <% if $Image.exists %>
        <figure class="ywli-text-media__media">
            <% with $Image.ScaleMaxWidth(1200) %>
                <img class="ywli-text-media__image" src="$URL" width="$Width" height="$Height" alt="$Up.ImageAltText.ATT"<% if $Up.IsFirstElement %> fetchpriority="high"<% else %> loading="lazy"<% end_if %> decoding="async">
            <% end_with %>
        </figure>
    <% end_if %>
    <div class="ywli-text-media__body">
        $Heading('ywli-text-media__title')
        <% if $Content %><div class="ywli-text-media__content">$Content</div><% end_if %>
        <% include YouWillLikeIT\\Elements\\CTALinks %>
    </div>
</div>
