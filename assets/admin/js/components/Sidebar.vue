<template>
    <div class="sidebar bg-blue-10" :class="{'closed': !isActive}">
        <div class="sidebar-label text-caption">
            Пользователи
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-menu__item">
                <span class="material-icons">badge</span>
                <router-link :to="{name: 'EmployeeList'}">
                    {{$loc('employeeList')}}
                </router-link>
            </li>
            <li class="sidebar-menu__item">
                <span class="material-icons">face</span>
                <router-link :to="{name: 'ClientList'}">
                    {{$loc('clientList')}}
                </router-link>
            </li>
            <li class="sidebar-menu__item">
                <span class="material-icons">photo</span>
                <router-link :to="{name: 'OrderList'}">
                    {{$loc('orderList')}}
                </router-link>
            </li>
            <li class="sidebar-menu__item">
                <span class="material-icons">photo</span>
                <router-link :to="{name: 'PaymentList'}">
                    {{$loc('paymentList')}}
                </router-link>
            </li>
            <li class="sidebar-menu__item">
                <span class="material-icons">photo</span>
                <router-link :to="{name: 'AreaList'}">
                    {{$loc('areaList')}}
                </router-link>
            </li>
            <li class="sidebar-menu__item">
                <span class="material-icons">photo</span>
                <router-link :to="{name: 'CategoryList'}">
                    {{$loc('categoryList')}}
                </router-link>
            </li>
            <li class="sidebar-menu__item">
                <span class="material-icons">photo</span>
                <router-link :to="{name: 'SpecialityList'}">
                    {{$loc('specialityList')}}
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
export default {
    name: 'Sidebar',
    props: {
        isActive: {
            type: Boolean,
        },
    },
};
</script>

