<%-- Basic markup only: native <details> is keyboard and screen-reader accessible without JS. --%>
<div class="ywli-faq">
    <% if $Heading || $Intro %>
        <header class="ywli-faq__header">
            $Heading('ywli-faq__title')
            <% if $Intro %><div class="ywli-faq__intro">$Intro</div><% end_if %>
        </header>
    <% end_if %>
    <% if $SortedItems.exists %>
        <div class="ywli-faq__items">
            <% loop $SortedItems %>
                <details class="ywli-faq__item" id="faq-$Up.ID-$ID">
                    <summary class="ywli-faq__question">$Question</summary>
                    <div class="ywli-faq__answer">$Answer</div>
                </details>
            <% end_loop %>
        </div>
    <% end_if %>
    <% include YouWillLikeIT\\Elements\\CTALinks %>
</div>
