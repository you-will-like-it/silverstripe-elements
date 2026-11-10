<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-cta">
    $Heading('ywli-cta__title')
    <% if $Content %><p class="ywli-cta__text">$Content</p><% end_if %>
    <% include YouWillLikeIT\\Elements\\CTALinks %>
</div>
