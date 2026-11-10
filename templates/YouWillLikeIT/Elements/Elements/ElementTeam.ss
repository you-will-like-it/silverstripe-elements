<%-- Basic markup only. Projects override this template in their theme. --%>
<div class="ywli-team">
    <% if $Heading || $Intro %>
        <header class="ywli-team__header">
            $Heading('ywli-team__title')
            <% if $Intro %><div class="ywli-team__intro">$Intro</div><% end_if %>
        </header>
    <% end_if %>
    <% if $SortedMembers.exists %>
        <ul class="ywli-team__members">
            <% loop $SortedMembers %>
                <li class="ywli-team__member">
                    <% if $Image.exists %>
                        <% with $Image.Fill(600,600) %>
                            <img class="ywli-team__image" src="$URL" width="$Width" height="$Height" alt="$Up.ImageAltText.ATT" loading="lazy" decoding="async">
                        <% end_with %>
                    <% end_if %>
                    <$Up.ChildHeadingTag class="ywli-team__name">$Name</$Up.ChildHeadingTag>
                    <% if $Role %><p class="ywli-team__role">$Role</p><% end_if %>
                    <% if $Bio %><p class="ywli-team__bio">$Bio</p><% end_if %>
                </li>
            <% end_loop %>
        </ul>
    <% end_if %>
    <% include YouWillLikeIT\\Elements\\CTALinks %>
</div>
