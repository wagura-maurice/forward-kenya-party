<template>
  <div class="pdf-viewer">
    <!-- Button trigger modal -->
    <button
      type="button"
      class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
      @click="openPdf"
    >
      <i class="fas fa-file-pdf mr-2 text-red-500"></i>
      {{ buttonText }}
    </button>

    <!-- PDF Viewer Modal -->
    <div v-if="showPdfViewer" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="closePdf"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-6xl sm:w-full">
          <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="flex justify-between items-center mb-4">
              <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                {{ title }}
              </h3>
              <button
                type="button"
                class="text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                @click="closePdf"
              >
                <span class="sr-only">Close</span>
                <i class="fas fa-times h-6 w-6"></i>
              </button>
            </div>
            
            <!-- PDF Viewer -->
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
              
              <!-- PDF Container -->
              <div class="overflow-auto bg-gray-100 dark:bg-gray-900 flex items-center justify-center relative" style="min-height: 70vh;">
                <!-- Loading Overlay -->
                <div v-if="isLoading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-10">
                  <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg flex flex-col items-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-green-500 mb-2"></div>
                    <p class="text-gray-700 dark:text-gray-300">Loading page {{ currentPage }}...</p>
                  </div>
                </div>
                
                <!-- Error State -->
                <div v-else-if="hasError" class="text-center p-8">
                  <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-900 rounded-lg p-6 max-w-md mx-auto">
                    <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-3"></i>
                    <h3 class="text-lg font-medium text-red-800 dark:text-red-200 mb-2">Error Loading Document</h3>
                    <p class="text-red-700 dark:text-red-300 text-sm mb-4">We couldn't load the requested page. Please try again.</p>
                    <button 
                      @click="loadPage(currentPage)"
                      class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      <i class="fas fa-sync-alt mr-2"></i> Retry
                    </button>
                  </div>
                </div>
                
                <!-- PDF Content -->
                <div v-else class="w-full h-full flex items-center justify-center p-4">
                  <img 
                    :src="currentPdfUrl"
                    :key="currentPage"
                    class="max-w-full max-h-[65vh] object-contain shadow-lg"
                    @load="onPdfLoad"
                    @error="onPdfError"
                    :alt="`Page ${currentPage} of ${totalPages}`"
                    @contextmenu.prevent
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 dark:bg-gray-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button
              type="button"
              class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
              @click="closePdf"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue';

export default {
  name: 'PdfViewer',
  
  props: {
    pdfUrl: {
      type: String,
      required: true,
    },
    title: {
      type: String,
      default: 'Document Viewer',
    },
    buttonText: {
      type: String,
      default: 'View Document',
    },
  },

  setup(props) {
    const showPdfViewer = ref(false);
    const isLoading = ref(false);
    const hasError = ref(false);
    
    // Create a versioned URL to prevent caching issues
    const currentPdfUrl = computed(() => {
      if (!props.pdfUrl) return '';
      
      const url = new URL(props.pdfUrl, window.location.origin);
      url.searchParams.set('v', Date.now()); // Add timestamp to prevent caching
      return url.toString();
    });
    
    const openPdf = () => {
      if (!props.pdfUrl) {
        console.error('PDF URL is required');
        return;
      }
      hasError.value = false;
      isLoading.value = true;
      showPdfViewer.value = true;
    };

    const closePdf = () => {
      showPdfViewer.value = false;
    };
    
    const onPdfLoad = () => {
      try {
        hasError.value = false;
      } catch (error) {
        console.error('Error processing PDF:', error);
        hasError.value = true;
      } finally {
        isLoading.value = false;
      }
    };
    
    const onPdfError = (error) => {
      console.error('Error loading PDF:', error);
      hasError.value = true;
      isLoading.value = false;
    };

    // Load PDF when component mounts
    onMounted(() => {
      // No keyboard navigation needed
    });

    return {
      showPdfViewer,
      currentPdfUrl,
      isLoading,
      hasError,
      openPdf,
      closePdf,
      onPdfLoad,
      onPdfError,
    };
  },
};
</script>

<style scoped>
/* Prevent text selection and right-click */
.pdf-viewer * {
  user-select: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  -webkit-touch-callout: none;
  -khtml-user-select: none;
}

/* Disable right-click context menu */
.pdf-viewer {
  -webkit-tap-highlight-color: transparent;
}

/* Disable text selection for the entire viewer */
.pdf-viewer {
  user-select: none;
}

/* Disable dragging images */
.pdf-viewer img {
  pointer-events: none;
  -webkit-user-drag: none;
  -khtml-user-drag: none;
  -moz-window-dragging: none;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Add a subtle transition for page changes */
.page-transition {
  transition: opacity 0.2s ease-in-out;
}

.page-transition.page-enter-active,
.page-transition.page-leave-active {
  transition: opacity 0.2s ease-in-out;
}

.page-transition.page-enter-from,
.page-transition.page-leave-to {
  opacity: 0;
}

/* Hide scrollbar but allow scrolling */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Dark mode styles */
.dark ::-webkit-scrollbar-track {
  background: #374151;
}

.dark ::-webkit-scrollbar-thumb {
  background: #4b5563;
}

.dark ::-webkit-scrollbar-thumb:hover {
  background: #6b7280;
}
</style>
