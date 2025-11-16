import { ComponentFixture, TestBed } from '@angular/core/testing';
import { AdminCarrerasPage } from './admin-carreras.page';

describe('AdminCarrerasPage', () => {
  let component: AdminCarrerasPage;
  let fixture: ComponentFixture<AdminCarrerasPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(AdminCarrerasPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
