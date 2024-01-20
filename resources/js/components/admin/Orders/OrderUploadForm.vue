<template>
  <loading-component v-if="loading"></loading-component>
  <div class="card" v-else>
    <div class="card-body">
      <display-errors v-if="errors" :errors="errors" :key="updateErrors"></display-errors>
      <form @submit.prevent="submitForm">
        <div class="row">
          <div class="col-lg-6">
            <div class="mb-3">
              <label :for="form.season_id.id" class="form-label">{{ form.season_id.label }}</label>
              <vue-select
                :name="form.season_id.name"
                :id="form.season_id.id"
                :items="form.season_id.items"
                :model.sync="form.season_id.value"
              >
              </vue-select>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="mb-3 d-flex flex-column">
              <label class="form-label">Файл</label>
              <div class="file-uploads file-uploads-html5 file-uploads-drop text-start" v-show="form.file.items.length <= 0">
                <!-- Добавление файл -->
                <div class="cursor-p">
                  <file-upload
                    ref="uploadFiles"
                    name="fileOrder"
                    v-model="form.file.items"
                    :headers="{'X-CSRF-TOKEN': token}"
                    @input-file="inputFile"
                    @input-filter="inputFileFilter"
                    post-action="/admin/orders/files/uploadFiles">
                    <a href="#" class="btn btn-primary btn__link cursor-p">+ Добавить</a>
                  </file-upload>
                </div>
              </div>
              <div v-for="(file, indexFile) in form.file.items" :key="indexFile" v-if="form.file.items.length > 0">
                <div class="object-wrapper object-file d-flex justify-content-between flex-wrap">
                  <div class="file-title text-center">{{ getFileName(file, 'name') }}</div>
                  <div class="photo__progress">
                    <span class="no-wrap" v-if="file.success">Файл загружен</span>
                    <span class="no-wrap" v-if="!file.success">Загрузка...</span>
                    <span v-if="!file.success" v-text="file.progress + '%'"></span>
                  </div>
                  <a href="#" class="btn btn-sm btn-primary remove-item" @click.prevent="removeFile(file, 'uploadFiles')">
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row">
              <div class="col-6">
                <button type="submit" class="btn btn-primary btn-sm btn-block" :disabled="!accessSubmit">Сохранить</button>
              </div>

              <div class="col-6">
                <a class="btn btn-light btn-sm btn-block" :href="backurl">Назад</a>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div>
            <a href="/files/sample.xlsx" target="_blank">Скачать образец файла загрузки</a>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import InputTextComponent from "../../FormInputs/InputTextComponent.vue";
import LoadingComponent from "../../LoadingComponent.vue";
import DisplayErrors from "../../DisplayErrors.vue";
import TextareaComponent from "../../FormInputs/TextAreaComponent.vue";
import VueSelect from "../../FormInputs/VueSelect.vue";
import FileUpload from 'vue-upload-component'

export default {
  name: "OrderUploadForm",
  components: {VueSelect, TextareaComponent, DisplayErrors, LoadingComponent, InputTextComponent, FileUpload},
  props: {
    id: {
      type: Number,
      default: 0,
    },
    backurl: '',
  },
  computed: {
    accessSubmit: function () {
      return this.form.season_id.value && this.form.file.items.length > 0
    },
  },
  data() {
    let token = document.head.querySelector('meta[name="csrf-token"]');
    return {
      loading: true,
      form: [],
      errors: null,
      updateErrors: 0,
      success: false,
      action: '',
      type: '',
      token: token.content,
      successUrl: '/admin/history/show/',
    }
  },
  mounted() {
    this.getData();
  },
  methods: {
    getData() {
      this.loading = true;
      axios.get('/admin/orders/get_order_form')
        .then((response) => {
          this.form = response.data;
          this.errors = null;
        })
        .catch(() => {
        })
        .finally(() => {
          this.loading = false;
        });
    },
    submitForm() {
      this.loading = true;
      axios.post('/admin/orders/store_order', this.form)
        .then((response) => {
          this.errors = null;
          if (response.data.status) {
            location.replace(this.successUrl + response.data.id)
          } else {
            this.errors = ['При загрузке файла с заказами произошла ошибка!'];
            if (response.data.validateError) {
              this.errors.push(response.data.errors);
            }
          }
        })
        .catch(() => {
          this.errors = ['При загрузке файла с заказами произошла ошибка!'];
          this.success = false;
        }).finally(() => {
        this.updateErrors++;
        this.loading = false;
      });
    },
    /**
     * Has changed
     */
    inputFile: function (newFile, oldFile) {
      // Automatic upload
      if (Boolean(newFile) !== Boolean(oldFile) || oldFile.error !== newFile.error) {
        if (!this.$refs.uploadFiles.active) {
          this.$refs.uploadFiles.active = true;
        }
      }
    },
    inputFileFilter: function (newFile, oldFile, prevent) {
      if (newFile && !oldFile) {
        // Filter non-image file
        if (!/\.(xlsx)$/i.test(newFile.name)) {
          this.errors = ['Недопустимый формат Файла'];
          setTimeout(() => {
            let validationMessages = document.getElementById('title');
            if (validationMessages) {
              validationMessages.scrollIntoView({block: "center", behavior: "smooth"});
            }
          }, 300);
          this.updateErrors++;
          return prevent();
        } else {
          this.errors = null;
          if (newFile && !newFile.blob) {
            // Create a blob field
            newFile.blob = '';
            let URL = window.URL || window.webkitURL;
            if (URL && URL.createObjectURL) {
              newFile.blob = URL.createObjectURL(newFile.file);
            }
          }
        }
      }
    },
    /**
     * Удаление файла
     * @param file
     * @param ref
     */
    removeFile(file, ref) {
      this.$refs[ref].remove(file);
    },
    getFileName(file, field = 'path') {
      let str;
      if (field === 'path') {
        str = file['path'].split('/').pop();
      } else {
        str = file['name'];
      }
      return str.slice(0, str.indexOf(str.split('.').pop()) - 1);
    },
  }
}
</script>

<style scoped>
.cursor-p {
  cursor: pointer;
}

.file-uploads {
  overflow: hidden;
  position: relative;
  text-align: center;
  display: inline-block;
}

.file-uploads.file-uploads-html5 input[type="file"] {
  overflow: hidden;
  position: fixed;
  width: 1px;
  height: 1px;
  z-index: -1;
  opacity: 0;
  cursor: pointer;
}

.file-uploads.file-uploads-html4 input, .file-uploads.file-uploads-html5 label {
  cursor: pointer !important;
}
</style>
