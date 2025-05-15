<template>
    <Head title="Shift Management"/>
    <MainLayout title="Shift Management">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addShiftAction" v-if="can('shift.create')">Add Shift</BsButton>
        </template>
        <div class="flex flex-row">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="id" @selection-changed="onSelectionChanged" @cell-dbl-click="editShiftAction($event.data)">
                <DxColumn data-field="shift_name" caption="Shift Name" :allowHeaderFiltering="false" />
                <DxColumn data-field="start_time" caption="Start Time" :allowHeaderFiltering="false" />
                <DxColumn data-field="end_time" caption="End Time" :allowHeaderFiltering="false" />
                <DxColumn data-field="is_active" caption="Status" cell-template="shift-status" :allowFiltering="true" :allowHeaderFiltering="false" data-type="boolean" false-text="Inactive" true-text="Active"/>
                <template #shift-status="{ data }">
                    <span v-if="data.data.is_active"
                        class="px-4 py-2 rounded-md bg-success text-white text-xs">Active</span>
                    <span v-else class="px-4 py-2 rounded-md bg-danger text-white text-xs">Inactive</span>
                </template>
                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false" :showInColumnChooser="false" :fixed="true" fixed-position="right" v-if="can('shift.update|shift.delete')"/>
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item v-if="can('shift.update')" @click="editShiftAction(data.data)" >
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit shift
                                </el-dropdown-item>
                                <el-dropdown-item v-if="!data.data.is_active && can('shift.update')" @click="switchShiftStatus(data.data, true)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Enable shift
                                </el-dropdown-item>
                                <el-dropdown-item v-else-if="can('shift.update')" @click="switchShiftStatus(data.data, false)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Disable shift
                                </el-dropdown-item>
                                <el-dropdown-item v-if="can('shift.delete')" @click="deleteShiftAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete shift
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </template>

                <!-- Custom toolbar -->
                <DxToolbar>
                    <DxItem location="before" template="buttonTemplate" />
                    <DxItem name="columnChooserButton" />
                    <DxItem name="exportButton" />
                    <DxItem widget="dxButton" :options="{ icon: 'refresh', onClick: refreshDatagrid }" />
                </DxToolbar>
                <template #buttonTemplate>
                    <div class="flex w-full">
                        <Transition name="fadetransition" mode="out-in" appear>
                            <div v-if="!itemSelected">
                                <!-- Table Header Action Here -->
                            </div>
                            <div v-else class="flex items-center border-2 border-primary-border rounded-full gap-1 text-sm">
                                <BsIconButton icon="x-mark" @click="clearSelection" />
                                <span class="font-bold mr-2">{{ dataSelected.length }} Selected</span>

                                <div class="flex items-center border-l-2 px-2 h-full gap-1">
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchShiftStatus(dataSelected, true)"  v-if="can('shift.update')">
                                        <BsIconButton icon="check-circle" class="text-success" />
                                        <span class="mr-2 font-semibold">Enable</span>
                                    </div>
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchShiftStatus(dataSelected, false)"  v-if="can('shift.update')">
                                        <BsIconButton icon="x-circle" class="text-danger" />
                                        <span class="mr-2 font-semibold">Disable</span>
                                    </div>
                                    <p class="font-semibold italic text-gray-700" v-if="!can('shift.update')">No Action</p>
                                </div>
                            </div>
                        </Transition>
                    </div>
                </template>
            </DxDataGrid>
        </div>


        <!-- Modal -->
        <el-dialog v-model="dialogFormVisible" width="500px" :append-to-body="true" :destroy-on-close="true"
            class="!rounded-xl">
            <template #header>
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} Shift</span>
            </template>
            <el-form ref="formShiftRef" :model="formShift" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item :error="getFormError('shift_name')" prop="shift_name" label="Shift name" :required="true">
                    <el-input v-model="formShift.shift_name" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
                <el-form-item :error="getFormError('time')" prop="time" label="Time" :required="true">
                    <el-time-picker
                        v-model="formShift.time" 
                        is-range
                        range-separator="To"
                        start-placeholder="Start time"
                        end-placeholder="End time"
                        format="HH:mm"
                        value-format="HH:mm"
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <BsButton class="flex-grow" type="primary-outline" @click="closeDialog">Cancel</BsButton>
                    <BsButton class="flex-grow" v-if="!editMode" type="primary" @click="addShiftSubmitAction">Submit</BsButton>
                    <BsButton class="flex-grow" v-if="editMode" type="primary" @click="editShiftSubmitAction">Update</BsButton>
                </span>
            </template> 
        </el-dialog>
    </MainLayout>

</template>

<script setup>
import MainLayout from '@/Layouts/MainLayout.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import BsButton from '@granule/Components/BsButton.vue';
import { ref, computed } from 'vue';
import { can } from '@granule/Core/Helpers/permission-check';
import CustomStore from "devextreme/data/custom_store";
import { dxLoad } from '@granule/Core/Helpers/dx-helpers';
import {
    DxColumn,
    DxDataGrid,
    DxItem,
    DxToolbar
} from 'devextreme-vue/data-grid';
import BsIcon from '@granule/Components/BsIcon.vue';
import BsIconButton from '@granule/Components/BsIconButton.vue';
import axios from 'axios';



const formShiftRef = ref();
const editMode = ref(false);
const dialogFormVisible = ref(false);
const formShiftErrors = ref([]);

const formShift = useForm({
    shift_id: '',
    shift_name: '',
    time: [],
});

function addShiftAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    formShift.shift_id = '';
    formShift.shift_name = '';
    formShift.time = [];
}

async function addShiftSubmitAction() {
    await formShiftRef.value.validate((valid, _) => {
        if (valid) {
            formShift.post(route('shift.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formShiftErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formShiftErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}

function editShiftAction(dataShift) {
    editMode.value = true;
    dialogFormVisible.value = true;

    formShift.shift_id =dataShift.id;
    formShift.shift_name =dataShift.shift_name;
    formShift.time =[dataShift.start_time, dataShift.end_time];
}

async function editShiftSubmitAction() {
    await formShiftRef.value.validate(async (valid, _) => {
        if (valid) {
            formShift.put(route('shift.update',formShift.shift_id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formShiftErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formShiftErrors.value = errors;
                    if('message' in errors){
                        ElMessage({
                            message: errors.message,
                            type: 'error',
                        });
                    }
                }
            });
        }
    });
}

function deleteShiftAction(dataShift) {
    ElMessageBox.confirm(
        'Are you sure to delete this shift ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('shift.delete',dataShift.id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formShiftErrors.value = errors;
                }
            });
        })
        .catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        })
}

function switchShiftStatus(dataShift, status) {
    if (Array.isArray(dataShift)) {
        ElMessageBox.confirm(
            'Are you sure to switch these shift\'s status to ' + (status ? 'Active' : 'Inactive') + ' ?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        ).then(() => {
            dataShift.forEach((shift) => {
                switchShiftStatus(shift, status);
            });
        }).catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
    } else {
        axios.put(route('shift.switch_status', dataShift.id), {
            is_active: status
        }).then((response) => {
            ElMessage({
                message: response.data.message,
                type: 'success',
            });
            refreshDatagrid();
        }).catch((error) => {
            ElMessage({
                message: error.response?.data?.error || 'Failed to switch status',
                type: 'error',
            });
        });
    }
}

function getFormError(field, errors = formShiftErrors.value) {
    if (!errors && !errors.length) {
        return false
    }
    if (errors[field]) {
        return errors[field]
    }
}

function closeDialog() {
    dialogFormVisible.value = false;
}


// ========================================================================
// Data source using server side processing
// ========================================================================
const dataKey = 'id'; //change to data primary key
const dataRoute = route('shift.data_processing') //change to data processing route
const dataSource = new CustomStore({
    key: dataKey,
    load: dxLoad(dataRoute).bind(this),
});  


// DEVEXTREME DATAGRID
const datagridRef = ref();
const dataSelected = ref([]);
var itemSelected = computed(() => dataSelected.value.length > 0);

// On Refresh Datagrid
function refreshDatagrid() {
    datagridRef.value.instance.refresh();
};

// On Selection Changed
function onSelectionChanged(data) {
    dataSelected.value = data.selectedRowsData;
};

// Clear Selection
function clearSelection() {
    const dataGrid = datagridRef.value.instance;
    dataGrid.clearSelection();
    dataSelected.value = [];
}

</script>