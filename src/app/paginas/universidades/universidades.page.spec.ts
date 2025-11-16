import { ComponentFixture, TestBed } from '@angular/core/testing';
import { UniversidadesPage } from './universidades.page';

describe('UniversidadesPage', () => {
  let component: UniversidadesPage;
  let fixture: ComponentFixture<UniversidadesPage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(UniversidadesPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
