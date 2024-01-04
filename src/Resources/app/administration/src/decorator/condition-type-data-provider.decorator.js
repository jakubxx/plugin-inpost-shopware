import { Application } from 'src/core/shopware';

Application.addServiceProviderDecorator('ruleConditionDataProviderService', (ruleConditionService) => {
  ruleConditionService.addCondition('weblives_locker_package', {
    component: 'weblives-condition-locker-package',
    label: 'weblives.condition.lockerPackage.label',
    scopes: ['cart']
  });

  ruleConditionService.addCondition('weblives_courier_package', {
    component: 'weblives-condition-courier-package',
    label: 'weblives.condition.courierPackage.label',
    scopes: ['cart']
  });

  return ruleConditionService;
});
