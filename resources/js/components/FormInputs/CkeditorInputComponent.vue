<template>
  <div>
    <div class="form-group">
      <label :for="id">{{ label }}</label>
      <textarea-component :name="name" :id="id" class="form-control" :class="classes + (errors ? 'is-invalid' : '')" v-model="model_value"></textarea-component>
      <div class="invalid-feedback d-block" v-if="errors">{{ errors }}</div>
    </div>
    <div v-for="(file, index) in attached_files" :key="index">
      <input type="hidden" name="attached_files[]" v-model="file.id">
    </div>
  </div>
</template>

<script>
/* global ClassicEditor */
import TextareaComponent from "./TextAreaComponent.vue";

export default {
  name: "CkeditorInputComponent",
  components: {TextareaComponent},
  props: {
    label: {
      type: String,
      default: 'Message'
    },
    id: {
      type: String,
      default: ''
    },
    name: {
      type: String,
      default: ''
    },
    classes: {
      type: String,
      default: ''
    },
    modelValue: {
      type: String,
      default: ''
    },
    errors: {
      type: String,
      default: ''
    },
    language: {
      type: String,
      default: 'ru'
    },
    upload_url: {
      type: String,
      default: ''
    },
    csrf_token: {
      type: String,
      default: ''
    },
  },
  emits: ['update:modelValue'],
  data() {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    return {
      model_value: this.modelValue,
      attached_files: [],
      option: {
        toolbar: {
          items: [
            'heading',
            '|',
            'bold',
            'italic',
            'link',
            'bulletedList',
            'numberedList',
            'removeFormat',
            '|',
            'imageInsert',
            'blockQuote',
            'insertTable',
            'mediaEmbed',
            'undo',
            'redo'
          ],
        },
        simpleUpload: {
          uploadUrl: '/files/uploadEditorPhoto',
          headers: {
            'X-CSRF-Token': token.content,
            'X-Requested-With': 'XMLHttpRequest',
          },
          withCredentials: false,
          savedCallback: (file) => {
            this.attached_files.push(file);
          },
        },
        licenseKey: '',
        language: 'ru'
      },
    }
  },
  mounted() {
    ClassicEditor
      .create(document.querySelector('#' + this.id), this.option)
      .then(editor => {
        window.editor = editor;
        editor.model.document.on('change:data', () => {
          this.model_value = editor.getData();
        });
      });
  },
  watch: {
    'model_value': function () {
      this.$emit('update:modelValue', this.model_value);
    }
  },
}
</script>
