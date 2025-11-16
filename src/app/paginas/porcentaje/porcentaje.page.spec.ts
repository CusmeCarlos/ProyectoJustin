import { ComponentFixture, TestBed } from '@angular/core/testing';
import { PorcentajePage } from './porcentaje.page';

describe('PorcentajePage', () => {
  let component: PorcentajePage;
  let fixture: ComponentFixture<PorcentajePage>;

  beforeEach(() => {
    fixture = TestBed.createComponent(PorcentajePage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
