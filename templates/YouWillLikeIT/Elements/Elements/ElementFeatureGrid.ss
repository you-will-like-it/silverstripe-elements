<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-feature-grid">
    <% if $Heading || $Intro %>
        <header class="ywli-feature-grid__header">
            $Heading('ywli-feature-grid__title')
            <% if $Intro %><p class="ywli-feature-grid__intro">$Intro</p><% end_if %>
        </header>
    <% end_if %>
    <% if $SortedItems.exists %>
        <ul class="ywli-feature-grid__items">
            <% loop $SortedItems %>
                <li class="ywli-feature-grid__item">
                    <% if $Image.exists %>
                        <% with $Image.ScaleMaxWidth(800) %>
                            <img class="ywli-feature-grid__image" src="$URL" width="$Width" height="$Height" alt="$Up.ImageAltText.ATT" loading="lazy" decoding="async">
                        <% end_with %>
                    <% end_if %>
                    <$Up.ChildHeadingTag class="ywli-feature-grid__item-title">$Title</$Up.ChildHeadingTag>
                    <% if $Text %><p class="ywli-feature-grid__text">$Text</p><% end_if %>
                    <% if $Link.exists %>
                        <a class="ywli-feature-grid__link" href="$Link.URL"<% if $Link.OpenInNew %> target="_blank" rel="noopener"<% end_if %>>$Link.Title</a>
                    <% end_if %>
                </li>
            <% end_loop %>
        </ul>
    <% end_if %>
    <% include YouWillLikeIT\\Elements\\CTALinks %>
</div>
