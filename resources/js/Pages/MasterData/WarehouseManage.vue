<template>
    <Head title="Warehouse Management"/>
    <MainLayout title="Warehouse Management">
        <template #header-action>
            <BsButton type="primary" icon="plus" @click="addWarehouseAction" v-if="can('warehouse.create')">Add Warehouse</BsButton>
        </template>
        <div class="flex flex-row">
            <DxDataGrid ref="datagridRef" :data-source="dataSource" key="id" @selection-changed="onSelectionChanged" @cell-dbl-click="editWarehouseAction($event.data)">
                <DxColumn data-field="warehouse_name" caption="Warehouse Name" :allowHeaderFiltering="false" />
                <DxColumn data-field="is_active" caption="Status" cell-template="warehouse-status" :allowFiltering="true" :allowHeaderFiltering="false" data-type="boolean" false-text="Inactive" true-text="Active"/>
                <template #warehouse-status="{ data }">
                    <span v-if="data.data.is_active"
                        class="px-4 py-2 rounded-md bg-success text-white text-xs">Active</span>
                    <span v-else class="px-4 py-2 rounded-md bg-danger text-white text-xs">Inactive</span>
                </template>
                <DxColumn cell-template="action" width="60" alignment="center" :allowExporting="false" :showInColumnChooser="false" :fixed="true" fixed-position="right" v-if="can('warehouse.update|warehouse.delete')"/>
                <template #action="{ data }">
                    <el-dropdown trigger="click" placement="bottom-end">
                        <span class="el-dropdown-link">
                            <BsIcon icon="ellipsis-vertical" />
                        </span>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item v-if="can('warehouse.update')" @click="editWarehouseAction(data.data)" >
                                    <BsIcon icon="pencil-square" class="mr-2" /> Edit warehouse
                                </el-dropdown-item>
                                <el-dropdown-item v-if="!data.data.is_active && can('warehouse.update')" @click="switchWarehouseStatus(data.data, true)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Enable warehouse
                                </el-dropdown-item>
                                <el-dropdown-item v-else-if="can('warehouse.update')" @click="switchWarehouseStatus(data.data, false)">
                                    <BsIcon icon="arrow-path-rounded-square" class="mr-2" /> Disable warehouse
                                </el-dropdown-item>
                                <el-dropdown-item v-if="can('warehouse.delete')" @click="deleteWarehouseAction(data.data)">
                                    <BsIcon icon="trash" class="mr-2" /> Delete warehouse
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
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchWarehouseStatus(dataSelected, true)"  v-if="can('warehouse.update')">
                                        <BsIconButton icon="check-circle" class="text-success" />
                                        <span class="mr-2 font-semibold">Enable</span>
                                    </div>
                                    <div class="flex items-center rounded-full hover:bg-gray-200 cursor-pointer" @click="switchWarehouseStatus(dataSelected, false)"  v-if="can('warehouse.update')">
                                        <BsIconButton icon="x-circle" class="text-danger" />
                                        <span class="mr-2 font-semibold">Disable</span>
                                    </div>
                                    <p class="font-semibold italic text-gray-700" v-if="!can('warehouse.update')">No Action</p>
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
                <span class="font-bold text-lg">{{ !editMode ? 'Create' : 'Edit' }} Warehouse</span>
            </template>
            <el-form ref="formWarehouseRef" :model="formWarehouse" label-width="200px" label-position="top"
                require-asterisk-position="right" autocomplete="off">
                <el-form-item :error="getFormError('warehouse_name')" prop="warehouse_name" label="Warehouse name" :required="true">
                    <el-input v-model="formWarehouse.warehouse_name" autocomplete="one-time-code" autocorrect="off"
                        spellcheck="false" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer flex">
                    <BsButton class="flex-grow" type="primary-outline" @click="closeDialog">Cancel</BsButton>
                    <BsButton class="flex-grow" v-if="!editMode" type="primary" @click="addWarehouseSubmitAction">Submit</BsButton>
                    <BsButton class="flex-grow" v-if="editMode" type="primary" @click="editWarehouseSubmitAction">Update</BsButton>
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



const formWarehouseRef = ref();
const editMode = ref(false);
const dialogFormVisible = ref(false);
const formWarehouseErrors = ref([]);

const formWarehouse = useForm({
    warehouse_id: '',
    warehouse_name: '',
});

function addWarehouseAction() {
    editMode.value = false;
    dialogFormVisible.value = true;

    formWarehouse.warehouse_id = '';
    formWarehouse.warehouse_name = '';
}

async function addWarehouseSubmitAction() {
    await formWarehouseRef.value.validate((valid, _) => {
        if (valid) {
            formWarehouse.post(route('warehouse.create'), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formWarehouseErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formWarehouseErrors.value = errors;
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

function editWarehouseAction(dataWarehouse) {
    editMode.value = true;
    dialogFormVisible.value = true;

    formWarehouse.warehouse_id =dataWarehouse.id;
    formWarehouse.warehouse_name =dataWarehouse.warehouse_name;
}

async function editWarehouseSubmitAction() {
    await formWarehouseRef.value.validate(async (valid, _) => {
        if (valid) {
            formWarehouse.put(route('warehouse.update',formWarehouse.warehouse_id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    formWarehouseErrors.value = [];
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formWarehouseErrors.value = errors;
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

function deleteWarehouseAction(dataWarehouse) {
    ElMessageBox.confirm(
        'Are you sure to delete this warehouse ?',
        'Warning',
        {
            confirmButtonText: 'OK',
            cancelButtonText: 'Cancel',
            type: 'warning',
        }
    )
        .then(() => {
            router.delete(route('warehouse.delete',dataWarehouse.id), {
                onSuccess: (response) => {
                    ElMessage({
                        message: response.props.flash.message,
                        type: 'success',
                    });
                    refreshDatagrid();
                    dialogFormVisible.value = false;
                },
                onError: (errors) => {
                    formWarehouseErrors.value = errors;
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

function switchWarehouseStatus(dataWarehouse, status) {
    if (Array.isArray(dataWarehouse)) {
        ElMessageBox.confirm(
            'Are you sure to switch these warehouses status to ' + (status ? 'Active' : 'Inactive') + ' ?',
            'Warning',
            {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning',
            }
        ).then(() => {
            dataWarehouse.forEach((warehouse) => {
                switchWarehouseStatus(warehouse, status);
            });
        }).catch(() => {
            ElMessage({
                type: 'info',
                message: 'Action Canceled',
            })
        });
    } else {
        axios.put(route('warehouse.switch_status', dataWarehouse.id), {
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

function getFormError(field, errors = formWarehouseErrors.value) {
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
const dataRoute = route('warehouse.data_processing') //change to data processing route
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