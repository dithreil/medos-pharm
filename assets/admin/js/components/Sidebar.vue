<template>
    <div class="sidebar bg-blue-10" :class="{'closed': !isActive}">
        <div class="sidebar-label text-caption">
            Пользователи
        </div>
        <ul class="sidebar-menu">
            <li class="sidebar-menu__item">
                <span class="material-icons">miscellaneous_services</span>
                <router-link :to="{name: 'ProducerList'}">
                    Производители
                </router-link>
            </li>
            <li class="sidebar-menu__item">
              <span class="material-icons">local_shipping</span>
              <router-link :to="{name: 'SupplierList'}">
                Поставщики
              </router-link>
            </li>
          <li class="sidebar-menu__item">
            <span class="material-icons">shopping_cart</span>
            <router-link :to="{name: 'StoreList'}">
              Торговые точки
            </router-link>
          </li>
          <li class="sidebar-menu__item">
            <span class="material-icons">description</span>
            <router-link :to="{name: 'NomenclatureList'}">
              Номенклатура
            </router-link>
          </li>
          <li class="sidebar-menu__item">
            <span class="material-icons">description</span>
            <router-link :to="{name: 'IncomeList'}">
              Поступления товаров
            </router-link>
          </li>
        </ul>
    </div>
</template>

<script lang="ts">
import {Component, Prop, Vue} from 'vue-property-decorator';


@Component
export default class Sidebar extends Vue {
  @Prop({type: Boolean, required: false}) readonly isActive!: boolean;
};
</script>

