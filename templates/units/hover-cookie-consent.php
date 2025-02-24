<?php

if (!aya_opt('site_cookie_consent_bool', 'basic')) return;

?>

<div x-data="cookieconsent" class="fixed right-0 bottom-0 mb-4 mr-4 z-10">

</div>
<script>
    document.addEventListener("alpine:init", () => {
        Alpine.data("cookieconsent", () => ({
            init() {},
        }));
    });
</script>