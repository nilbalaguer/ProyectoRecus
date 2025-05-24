<script setup>
import { useConfirm } from "primevue/useconfirm";
import { useToast } from "primevue/usetoast";

/*
 * Para recojer el valor hay que usar @confirmed = ((result)=>{}); (bool    )
 */

const confirm = useConfirm();
const toast = useToast();

const emit = defineEmits(['confirmed'])

const props = defineProps({
    name: {
        default: 'Confirm'
    },
    header:
    {
        default: "Confirmation"
    },
    positive_option:
    {
        default: "Save"
    },
    negative_option:
    {
        default: "Cancel"
    },
    positive_severity:
    {
        default: ""
    },
    negative_severity:
    {
        default: "secondary"
    },
    button_class:
    {
        default: ""
    }
});

const dialog = () => {
    console.log("Open dialog aaaaaaaaaaaaaaaaa")
    return new Promise((resolve) => {
        confirm.require({
            message: 'Are you sure you want to proceed?',
            header: props.header,
            icon: 'pi pi-exclamation-triangle',
            rejectProps: {
                label: props.negative_option,
                severity: props.negative_severity
            },
            acceptProps: {
                label: props.positive_option,
                severity: props.positive_severity
            },
            accept: () => {
                // toast.add({ severity: 'info', summary: 'Confirmed', detail: 'You have accepted', life: 3000 });
                emit('confirmed', true);
            },
            reject: () => {
                // toast.add({ severity: 'error', summary: 'Rejected', detail: 'You have rejected', life: 3000 });
                emit('confirmed', false);
            }
        });
    });

}

</script>

<template>
    <Toast />
    <ConfirmDialog></ConfirmDialog>

    <Button  @click="dialog()" :class="props.button_class">{{ props.name }}</Button>
</template>