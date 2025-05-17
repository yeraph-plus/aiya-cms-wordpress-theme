<script setup></script>

<template>
  <!-- Cards: User -->
  <div
    class="flex flex-col overflow-hidden rounded-lg bg-white shadow-xs dark:bg-gray-800 dark:text-gray-100"
  >
    <!-- Card Cover/Avatar -->
    <div
      class="mb-8 bg-cover"
      style="
        background-image: url(&quot;https://cdn.tailkit.com/media/placeholders/photo-JgOeRuGD_Y4-800x400.jpg&quot;);
      "
    >
      <div class="flex h-32 items-end justify-center">
        <div
          class="-mb-12 rounded-full bg-gray-200/50 p-2 dark:bg-gray-600/50"
        >
          <img
            src="https://cdn.tailkit.com/media/placeholders/avatar-c_GmwfHBDzk-160x160.jpg"
            alt="User Avatar"
            class="inline-block size-20 rounded-full"
          />
        </div>
      </div>
    </div>
    <!-- END Card Cover/Avatar -->

    <!-- Card Body -->
    <div class="grow p-5 text-center">
      <h3 class="mt-3 mb-1 text-lg font-semibold">Antony Jaramillo</h3>
      <p class="text-sm font-medium text-gray-600 dark:text-gray-400">
        Web Developer ∙ 12 Projects
      </p>
    </div>
    <!-- END Card Body -->
  </div>
  <!-- END Cards: User -->
</template>