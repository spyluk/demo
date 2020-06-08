<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Example Component</div>
                    <div class="card-body">
                        I'm an example component.
                        <vue-dropzone ref="myVueDropzone" ></vue-dropzone>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import vue2Dropzone from 'vue2-dropzone'
import 'vue2-dropzone/dist/vue2Dropzone.min.css'

    export default {
     components: {
        vueDropzone: vue2Dropzone
      },
      data: function () {
        return {
          dropzoneOptions: {
              url: '/formSubmit',
              headers: {
                "X-CSRF-TOKEN": document.head.querySelector("[name=csrf-token]").content
               }
          }
        }
      },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>