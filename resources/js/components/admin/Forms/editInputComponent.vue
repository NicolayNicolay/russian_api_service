<template>
  <input
    v-if="permission"
    type="text"
    :ref="'input_' + type + '_' + orderId"
    class="form-control editing-input"
    :id="'input_' + type + '_' + orderId"
    alt="Нажмите дважды для редактирования"
    v-model="newValue"
    v-on:dblclick="editInput($event)"
    @change="sendData($event)"
    readonly
  >
  <input
    v-else
    type="text"
    :ref="'input_' + type + '_' + orderId"
    class="form-control editing-input"
    :id="'input_' + type + '_' + orderId"
    alt="Нажмите дважды для редактирования"
    v-model="newValue"
    v-on:dblclick="sendAlert($event)"
    readonly
  >
</template>

<script>
import {toasts} from "../../../helpers/toasts.js";

export default {
  name: "editInputComponent",
  props: {
    "part": String,
    "orderId": String,
    "type": String,
    "inputValue": String,
    "permission": String,
  },
  data()
  {
    return {
      newValue: this.inputValue
    }
  },
  computed: {},
  methods: {
    editInput(e)
    {
      e.target.readOnly = false;
      e.target.classList.add('active');
    },
    sendData(e)
    {
      e.target.readOnly = true;
      e.target.classList.remove('active');

      axios.post(`/admin/order/editPrice`, {
        partNumber: this.part,
        orderId: this.orderId,
        type: this.type,
        newValue: this.newValue
      })
        .then(response => {
          toasts.showToast(`Изменения сохранены!`,
            'toastAlertSuccess',
            ['text-white', 'bg-success'])
        })
        .catch(error => {
          toasts.showToast(`Произошла ошибка, попробуйте ещё раз!`,
            'toastAlertDanger',
            ['text-white', 'bg-danger'])
        });
    },
    sendAlert(e)
    {
      toasts.showToast(`У вас нет прав на совершение этого действия`,
        'toastAlertDanger',
        ['text-white', 'bg-danger'])
    },
  },
  mounted()
  {
    // Когда инпут не в фокусе и не изменился просто ставим readonly
    let ignoreClickOnMeElement = document.getElementById('input_' + this.type + '_' + this.orderId);

    ignoreClickOnMeElement.addEventListener('blur', function (event) {
      ignoreClickOnMeElement.classList.remove('active');
      ignoreClickOnMeElement.readOnly = true;
    });
  }
}
</script>

<style scoped>

</style>
