<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-hero">
    <% if $Image.exists %>
        <% with $Image.ScaleMaxWidth(1920) %>
            <img class="ywli-hero__image" src="$URL" width="$Width" height="$Height" alt="$Up.ImageAltText.ATT"<% if $Up.IsFirstElement %> fetchpriority="high"<% else %> loading="lazy"<% end_if %> decoding="async">
        <% end_with %>
    <% end_if %>
    <div class="ywli-hero__body">
        $Heading('ywli-hero__title')
        <% if $Lead %><p class="ywli-hero__lead">$Lead</p><% end_if %>
        <% include YouWillLikeIT\\Elements\\CTALinks %>
    </div>
</div>
