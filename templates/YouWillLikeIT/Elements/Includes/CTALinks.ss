<% if $SortedCTALinks.exists %>
    <ul class="ywli-cta-links">
        <% loop $SortedCTALinks %>
            <li class="ywli-cta-links__item">
                <a class="ywli-cta-links__link<% if $IsFirst %> ywli-cta-links__link--primary<% end_if %>" href="$URL"<% if $OpenInNew %> target="_blank" rel="noopener"<% end_if %>>$Title</a>
            </li>
        <% end_loop %>
    </ul>
<% end_if %>
