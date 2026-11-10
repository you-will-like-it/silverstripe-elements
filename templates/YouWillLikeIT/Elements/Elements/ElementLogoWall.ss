<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-logo-wall">
    $Heading('ywli-logo-wall__title')
    <% if $SortedLogos.exists %>
        <ul class="ywli-logo-wall__items">
            <% loop $SortedLogos %>
                <% if $Image.exists %>
                    <li class="ywli-logo-wall__item">
                        <% if $Link.exists %><a class="ywli-logo-wall__link" href="$Link.URL"<% if $Link.OpenInNew %> target="_blank" rel="noopener"<% end_if %>><% end_if %>
                        <% if $Image.Extension == 'svg' %>
                            <img class="ywli-logo-wall__image" src="$Image.URL" alt="$Name.ATT" loading="lazy" decoding="async">
                        <% else %>
                            <% with $Image.ScaleMaxHeight(160) %>
                                <img class="ywli-logo-wall__image" src="$URL" width="$Width" height="$Height" alt="$Up.Name.ATT" loading="lazy" decoding="async">
                            <% end_with %>
                        <% end_if %>
                        <% if $Link.exists %></a><% end_if %>
                    </li>
                <% end_if %>
            <% end_loop %>
        </ul>
    <% end_if %>
</div>
