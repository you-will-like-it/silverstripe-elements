<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-testimonials">
    $Heading('ywli-testimonials__title')
    <% if $SortedItems.exists %>
        <ul class="ywli-testimonials__items">
            <% loop $SortedItems %>
                <li class="ywli-testimonials__item">
                    <figure class="ywli-testimonial">
                        <% if $Rating %><p class="ywli-testimonial__rating" role="img" aria-label="$RatingLabel.ATT"><span aria-hidden="true">$Stars</span></p><% end_if %>
                        <blockquote class="ywli-testimonial__quote"><p>$Quote</p></blockquote>
                        <figcaption class="ywli-testimonial__caption">
                            <span class="ywli-testimonial__author">$Author</span><% if $Context %>, <span class="ywli-testimonial__context">$Context</span><% end_if %><% if $Source %> <span class="ywli-testimonial__source">($Source)</span><% end_if %>
                        </figcaption>
                    </figure>
                </li>
            <% end_loop %>
        </ul>
    <% end_if %>
</div>
